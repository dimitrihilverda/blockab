<?php
/**
 * Get statistics for a test
 *
 * @package blockab
 * @subpackage processors
 */

class babStatsGetStatsProcessor extends modProcessor {
    public function process() {
        $testId = $this->getProperty('test');
        $days = (int)$this->getProperty('days', 0); // 0 = all time

        if (empty($testId)) {
            return $this->failure($this->modx->lexicon('blockab.error.test_not_found'));
        }

        $test = $this->modx->getObject('babTest', $testId);
        if (!$test) {
            return $this->failure($this->modx->lexicon('blockab.error.test_not_found'));
        }

        $variations = $this->getVariationsStats($test, $days);

        $stats = array(
            'test' => $test->toArray(),
            'variations' => array(),
            'total_picks' => 0,
            'total_conversions' => 0,
            'days' => $days,
        );

        $bestRate = 0;
        foreach ($variations as $variationId => $variation) {
            $stats['variations'][] = $variation;
            $stats['total_picks'] += $variation['picks'];
            $stats['total_conversions'] += $variation['conversions'];
            if ($variation['conversionrate'] > $bestRate) {
                $bestRate = $variation['conversionrate'];
            }
        }

        $stats['total_conversion_rate'] = ($stats['total_picks'] > 0)
            ? round(($stats['total_conversions'] / $stats['total_picks']) * 100, 2)
            : 0;

        $stats['best_rate'] = round($bestRate, 2);

        // Calculate statistical significance
        $stats['significance'] = $this->calculateSignificance($stats['variations']);

        // Get history data
        $historyDays = ($days > 0) ? $days : 30;
        $stats['history'] = $this->getHistory($testId, $historyDays);

        return $this->success('', $stats);
    }

    /**
     * Get variation stats with optional date filtering
     *
     * @param babTest $test
     * @param int $days - 0 = all time
     * @return array
     */
    protected function getVariationsStats(babTest $test, $days = 0) {
        $testId = $test->get('id');

        $variations = $this->modx->getCollection('babVariation', array(
            'test' => $testId,
            'active' => 1,
        ));

        $result = array();
        foreach ($variations as $variation) {
            $variationId = $variation->get('id');

            $where = array(
                'test' => $testId,
                'variation' => $variationId,
            );

            if ($days > 0) {
                $where['date:>='] = date('Ymd', strtotime("-{$days} days"));
            }

            $picks = $this->modx->blockab->getSum('babPick', $where);
            $conversions = $this->modx->blockab->getSum('babConversion', $where);

            $conversionRate = ($picks > 0) ? ($conversions / $picks) * 100 : 0;

            $result[$variationId] = array(
                'id' => $variationId,
                'name' => $variation->get('name'),
                'variant_key' => $variation->get('variant_key'),
                'weight' => $variation->get('weight'),
                'picks' => $picks,
                'conversions' => $conversions,
                'conversionrate' => $conversionRate,
            );
        }

        return $result;
    }

    /**
     * Calculate chi-square statistical significance across all variants
     *
     * @param array $variations
     * @return array
     */
    protected function calculateSignificance(array $variations) {
        $result = array(
            'significant' => false,
            'confidence' => 0,
            'winner' => null,
            'chi_squared' => 0,
            'min_samples_met' => false,
        );

        if (count($variations) < 2) {
            return $result;
        }

        $totalViews = 0;
        $totalConversions = 0;
        foreach ($variations as $v) {
            $totalViews += $v['picks'];
            $totalConversions += $v['conversions'];
        }

        // Insufficient data
        if ($totalViews < 100 || $totalConversions < 5) {
            return $result;
        }

        $result['min_samples_met'] = true;

        $overallRate = $totalConversions / $totalViews;

        // Chi-square: compare each variant's observed vs expected conversions
        $chiSquared = 0;
        foreach ($variations as $v) {
            $n = $v['picks'];
            if ($n == 0) continue;

            $obsConv    = $v['conversions'];
            $obsNoConv  = $n - $obsConv;
            $expConv    = $n * $overallRate;
            $expNoConv  = $n * (1 - $overallRate);

            if ($expConv > 0) {
                $chiSquared += pow($obsConv - $expConv, 2) / $expConv;
            }
            if ($expNoConv > 0) {
                $chiSquared += pow($obsNoConv - $expNoConv, 2) / $expNoConv;
            }
        }

        $result['chi_squared'] = round($chiSquared, 4);

        // Degrees of freedom = variants - 1, capped at 5
        $df = min(count($variations) - 1, 5);

        // Critical values for chi-square distribution
        $criticalValues = array(
            1 => array(95 => 3.841,  99 => 6.635),
            2 => array(95 => 5.991,  99 => 9.210),
            3 => array(95 => 7.815,  99 => 11.345),
            4 => array(95 => 9.488,  99 => 13.277),
            5 => array(95 => 11.070, 99 => 15.086),
        );

        $confidence = 0;
        if (isset($criticalValues[$df])) {
            if ($chiSquared >= $criticalValues[$df][99]) {
                $confidence = 99;
            } elseif ($chiSquared >= $criticalValues[$df][95]) {
                $confidence = 95;
            }
        }

        $result['confidence'] = $confidence;
        $result['significant'] = ($confidence >= 95);

        // Determine winner: highest conversion rate with >= 30 views
        $bestRate = -1;
        $winner = null;
        foreach ($variations as $v) {
            if ($v['picks'] >= 30 && $v['conversionrate'] > $bestRate) {
                $bestRate = $v['conversionrate'];
                $winner = $v['variant_key'];
            }
        }
        $result['winner'] = $winner;

        return $result;
    }

    /**
     * Get historical data
     *
     * @param int $testId
     * @param int $days
     * @return array
     */
    protected function getHistory($testId, $days = 30) {
        $startDate = date('Ymd', strtotime("-{$days} days"));
        $endDate = date('Ymd');

        $c = $this->modx->newQuery('babPick');
        $c->where(array(
            'test' => $testId,
            'date:>=' => $startDate,
            'date:<=' => $endDate
        ));
        $c->sortby('date', 'ASC');
        $picks = $this->modx->getCollection('babPick', $c);

        $c = $this->modx->newQuery('babConversion');
        $c->where(array(
            'test' => $testId,
            'date:>=' => $startDate,
            'date:<=' => $endDate
        ));
        $c->sortby('date', 'ASC');
        $conversions = $this->modx->getCollection('babConversion', $c);

        $history = array();

        foreach ($picks as $pick) {
            $date = $pick->get('date');
            if (!isset($history[$date])) {
                $history[$date] = array('date' => $date, 'picks' => 0, 'conversions' => 0);
            }
            $history[$date]['picks'] += $pick->get('amount');
        }

        foreach ($conversions as $conversion) {
            $date = $conversion->get('date');
            if (!isset($history[$date])) {
                $history[$date] = array('date' => $date, 'picks' => 0, 'conversions' => 0);
            }
            $history[$date]['conversions'] += $conversion->get('amount');
        }

        return array_values($history);
    }
}

return 'babStatsGetStatsProcessor';

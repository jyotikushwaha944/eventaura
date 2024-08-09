<?php
function initializeMatrix($rows, $cols) {
    $matrix = [];
    for ($i = 0; $i < $rows; $i++) {
        for ($j = 0; $j < $cols; $j++) {
            $matrix[$i][$j] = rand(0, 5) / 5; // Random values between 0 and 1
        }
    }
    return $matrix;
}


function dotProduct($vector1, $vector2) {
    return array_sum(array_map(function ($x, $y) {
        return $x * $y;
    }, $vector1, $vector2));
}

function multiplyMatrices($P, $Q) {
    $result = [];
    for ($i = 0; $i < count($P); $i++) {
        for ($j = 0; $j < count($Q[0]); $j++) {
            $result[$i][$j] = dotProduct($P[$i], array_column($Q, $j));
        }
    }
    return $result;
}

function matrixFactorization($R, $K, $steps = 5000, $alpha = 0.002, $beta = 0.02) {
    $N = count($R);
    $M = count($R[array_key_first($R)]); // Use the first available key to determine the number of columns
    

    $P = initializeMatrix($N, $K);
    $Q = initializeMatrix($M, $K);
    $Q = array_map(null, ...$Q); // Transpose Q

    for ($step = 0; $step < $steps; $step++) {
        for ($i = 0; $i < $N; $i++) {
            for ($j = 0; $j < $M; $j++) {
                if (isset($R[$i][$j]) && $R[$i][$j] > 0) { // Check if value exists
                    $eij = $R[$i][$j] - dotProduct($P[$i], $Q[$j]);
                    for ($k = 0; $k < $K; $k++) {
                        $P[$i][$k] += $alpha * (2 * $eij * $Q[$j][$k] - $beta * $P[$i][$k]);
                        $Q[$j][$k] += $alpha * (2 * $eij * $P[$i][$k] - $beta * $Q[$j][$k]);
                    }
                }
            }
        }
    }

    return [multiplyMatrices($P, $Q), $P, $Q];
}

?>

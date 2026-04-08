<?php
/**
 * Algorithms Collection for Cakeria Project
 * BCA 6th Semester - Data Structures & Algorithms
 * 
 * This file contains implementations of:
 * 1. Merge Sort - O(n log n) sorting algorithm
 * 2. Quick Sort - O(n log n) average case sorting algorithm
 * 3. Binary Search - O(log n) searching algorithm
 * 4. KMP Algorithm - O(n + m) pattern matching algorithm
 */

// ========================
// 1. MERGE SORT ALGORITHM
// ========================
/**
 * Merge Sort - Divide and Conquer Sorting Algorithm
 * Time Complexity: O(n log n)
 * Space Complexity: O(n)
 * 
 * @param array $arr - Array to be sorted
 * @param int $left - Left index
 * @param int $right - Right index
 * @param string $key - Array key to sort by
 * @param string $order - 'asc' for ascending, 'desc' for descending
 * @return void - Sorts array in place
 */
function mergeSort(&$arr, $left, $right, $key = null, $order = 'asc') {
    if ($left < $right) {
        $mid = intdiv($left + $right, 2);
        
        // Sort left half
        mergeSort($arr, $left, $mid, $key, $order);
        
        // Sort right half
        mergeSort($arr, $mid + 1, $right, $key, $order);
        
        // Merge sorted halves
        merge($arr, $left, $mid, $right, $key, $order);
    }
}

/**
 * Merge helper function for Merge Sort
 */
function merge(&$arr, $left, $mid, $right, $key = null, $order = 'asc') {
    $leftArr = array_slice($arr, $left, $mid - $left + 1);
    $rightArr = array_slice($arr, $mid + 1, $right - $mid);
    
    $i = 0;
    $j = 0;
    $k = $left;
    
    while ($i < count($leftArr) && $j < count($rightArr)) {
        $leftVal = ($key === null) ? $leftArr[$i] : $leftArr[$i][$key];
        $rightVal = ($key === null) ? $rightArr[$j] : $rightArr[$j][$key];
        
        $shouldSwap = ($order === 'asc') ? ($leftVal <= $rightVal) : ($leftVal >= $rightVal);
        
        if ($shouldSwap) {
            $arr[$k++] = $leftArr[$i++];
        } else {
            $arr[$k++] = $rightArr[$j++];
        }
    }
    
    while ($i < count($leftArr)) {
        $arr[$k++] = $leftArr[$i++];
    }
    
    while ($j < count($rightArr)) {
        $arr[$k++] = $rightArr[$j++];
    }
}


// ========================
// 3. BINARY SEARCH ALGORITHM
// ========================
/**
 * Binary Search - Efficient Searching Algorithm
 * Time Complexity: O(log n)
 * Requires: Array must be sorted
 * 
 * @param array $arr - Sorted array to search in
 * @param mixed $target - Value to search for
 * @param string $key - Array key to search by (if array of objects/arrays)
 * @return int - Index of target if found, -1 otherwise
 */
function binarySearch($arr, $target, $key = null) {
    $left = 0;
    $right = count($arr) - 1;
    
    while ($left <= $right) {
        $mid = intdiv($left + $right, 2);
        $midVal = ($key === null) ? $arr[$mid] : $arr[$mid][$key];
        
        if ($midVal == $target) {
            return $mid;
        } elseif ($midVal < $target) {
            $left = $mid + 1;
        } else {
            $right = $mid - 1;
        }
    }
    
    return -1;
}

/**
 * Binary Search (Range) - Find all items in a range
 * 
 * @param array $arr - Sorted array
 * @param mixed $start - Start value
 * @param mixed $end - End value
 * @param string $key - Array key to search by
 * @return array - Array of matching items
 */
function binarySearchRange($arr, $start, $end, $key = null) {
    $result = [];
    
    foreach ($arr as $item) {
        $val = ($key === null) ? $item : $item[$key];
        if ($val >= $start && $val <= $end) {
            $result[] = $item;
        }
    }
    
    return $result;
}

// ========================
// 4. KMP ALGORITHM (PATTERN MATCHING)
// ========================
/**
 * Build KMP Failure Table
 * Time Complexity: O(m) where m is pattern length
 * 
 * @param string $pattern - Pattern to search for
 * @return array - Failure function table
 */
function buildKMPTable($pattern) {
    $m = strlen($pattern);
    $table = array_fill(0, $m, 0);
    $j = 0;
    
    for ($i = 1; $i < $m; $i++) {
        while ($j > 0 && $pattern[$i] != $pattern[$j]) {
            $j = $table[$j - 1];
        }
        
        if ($pattern[$i] == $pattern[$j]) {
            $j++;
        }
        
        $table[$i] = $j;
    }
    
    return $table;
}

/**
 * KMP Algorithm - Pattern Matching
 * Time Complexity: O(n + m) where n is text length, m is pattern length
 * Space Complexity: O(m)
 * 
 * @param string $text - Text to search in
 * @param string $pattern - Pattern to search for
 * @param bool $caseInsensitive - Case-insensitive search
 * @return array - All indices where pattern is found
 */
function kmpSearch($text, $pattern, $caseInsensitive = true) {
    if ($caseInsensitive) {
        $text = strtolower($text);
        $pattern = strtolower($pattern);
    }
    
    $n = strlen($text);
    $m = strlen($pattern);
    
    if ($m == 0 || $m > $n) {
        return [];
    }
    
    $table = buildKMPTable($pattern);
    $matches = [];
    $j = 0;
    
    for ($i = 0; $i < $n; $i++) {
        while ($j > 0 && $text[$i] != $pattern[$j]) {
            $j = $table[$j - 1];
        }
        
        if ($text[$i] == $pattern[$j]) {
            $j++;
        }
        
        if ($j == $m) {
            $matches[] = $i - $m + 1;
            $j = $table[$j - 1];
        }
    }
    
    return $matches;
}

/**
 * KMP Search in Array of Strings
 * Searches for pattern in each string of an array
 * 
 * @param array $items - Array of strings to search
 * @param string $pattern - Pattern to search for
 * @param string $key - Key to search in (for associative arrays)
 * @return array - Items containing the pattern
 */
function kmpSearchArray($items, $pattern, $key = null) {
    $result = [];
    $pattern = strtolower($pattern);
    
    foreach ($items as $item) {
        $searchText = ($key === null) ? $item : (isset($item[$key]) ? $item[$key] : '');
        $searchText = strtolower((string)$searchText);
        
        $matches = kmpSearch($searchText, $pattern, false);
        
        if (count($matches) > 0) {
            $result[] = $item;
        }
    }
    
    return $result;
}

// ========================
// HELPER FUNCTIONS
// ========================

/**
 * Check if array is sorted
 */
function isSorted($arr, $key = null, $order = 'asc') {
    for ($i = 0; $i < count($arr) - 1; $i++) {
        $current = ($key === null) ? $arr[$i] : $arr[$i][$key];
        $next = ($key === null) ? $arr[$i + 1] : $arr[$i + 1][$key];
        
        if ($order === 'asc' && $current > $next) {
            return false;
        }
        if ($order === 'desc' && $current < $next) {
            return false;
        }
    }
    return true;
}

/**
 * Convert database result to array
 */
function resultToArray($mysqli_result) {
    $array = [];
    while ($row = mysqli_fetch_assoc($mysqli_result)) {
        $array[] = $row;
    }
    return $array;
}

?>

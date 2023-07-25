<?php

/**
 * Get the category's color.
 *
 * @param $name
 * @return string|void
 */
function categoryColor($name)
{
    switch ($name) {
        case 'content':
            return 'success';
        case 'website':
            return 'danger';
        case 'marketing':
            return 'info';
        case 'social':
            return 'warning';
        case 'custom':
            return 'dark';
    }
}

/**
 * Encode a string for Quill display.
 *
 * @return string
 */
function encodeQuill($input)
{
    return "<p>" . str_replace("\n\n", "<p><br></p>", $input) . "</p>";
}

/**
 * Return the total words count.
 *
 * @param $text
 * @return float
 */
function wordsCount($text)
{
    $words = array_filter(explode(' ', preg_replace('/[^\w]/ui', ' ', mb_strtolower(trim($text)))));

    $wordsCount = 0;
    foreach ($words as $word) {
        $wordsCount += wordCount($word);
    }
    return round($wordsCount);
}

/**
 * Parse a word and return its word count based on a symbol to word ratio.
 *
 * @param $word
 * @return float|int
 */
function wordCount($word)
{
    foreach (config('completions.ratios') as $ratio) {
        if (preg_match('/\p{' . implode('}|\p{', $ratio['scripts']) . '}/ui', $word)) {
            return mb_strlen($word) * $ratio['value'];
        }
    }

    return 1;
}

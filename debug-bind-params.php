<?php
// Debug bind_param parameter mapping

echo "<h2>Detailed Parameter Analysis</h2>\n";

// Field definitions with their types
$fields_with_description = [
    'title' => 's',           // 1
    'description' => 's',     // 2  
    'price_label' => 's',     // 3
    'price_value' => 's',     // 4
    'price_unit' => 's',      // 5
    'icon_class' => 's',      // 6
    'features' => 's',        // 7
    'featured' => 'i',        // 8
    'button_text' => 's',     // 9
    'button_link' => 's',     // 10
    'hotel' => 's',           // 11
    'pesawat' => 's',         // 12
    'price_quad' => 's',      // 13
    'price_triple' => 's',    // 14
    'price_double' => 's',    // 15
    'id' => 'i'               // 16 (for UPDATE WHERE clause)
];

echo "<h3>UPDATE with description field mapping:</h3>\n";
echo "<table border='1' style='border-collapse: collapse;'>\n";
echo "<tr><th>Position</th><th>Field</th><th>Type</th><th>Variable</th></tr>\n";

$position = 1;
$bind_string = '';
foreach ($fields_with_description as $field => $type) {
    $variable = ($field === 'id') ? '$id' : '$' . $field;
    echo "<tr>";
    echo "<td>$position</td>";
    echo "<td>$field</td>";  
    echo "<td>$type</td>";
    echo "<td>$variable</td>";
    echo "</tr>\n";
    $bind_string .= $type;
    $position++;
}
echo "</table>\n";

echo "<p><strong>Correct bind_param string:</strong> '$bind_string' (length: " . strlen($bind_string) . ")</p>\n";

// Fields without description
$fields_without_description = [
    'title' => 's',           // 1
    'price_label' => 's',     // 2
    'price_value' => 's',     // 3
    'price_unit' => 's',      // 4
    'icon_class' => 's',      // 5
    'features' => 's',        // 6
    'featured' => 'i',        // 7
    'button_text' => 's',     // 8
    'button_link' => 's',     // 9
    'hotel' => 's',           // 10
    'pesawat' => 's',         // 11
    'price_quad' => 's',      // 12
    'price_triple' => 's',    // 13
    'price_double' => 's',    // 14
    'id' => 'i'               // 15 (for UPDATE WHERE clause)
];

echo "<h3>UPDATE without description field mapping:</h3>\n";
echo "<table border='1' style='border-collapse: collapse;'>\n";
echo "<tr><th>Position</th><th>Field</th><th>Type</th><th>Variable</th></tr>\n";

$position = 1;
$bind_string_no_desc = '';
foreach ($fields_without_description as $field => $type) {
    $variable = ($field === 'id') ? '$id' : '$' . $field;
    echo "<tr>";
    echo "<td>$position</td>";
    echo "<td>$field</td>";  
    echo "<td>$type</td>";
    echo "<td>$variable</td>";
    echo "</tr>\n";
    $bind_string_no_desc .= $type;
    $position++;
}
echo "</table>\n";

echo "<p><strong>Correct bind_param string:</strong> '$bind_string_no_desc' (length: " . strlen($bind_string_no_desc) . ")</p>\n";

echo "<hr>\n";
echo "<h3>Summary of Correct bind_param Strings:</h3>\n";
echo "<ul>\n";
echo "<li><strong>UPDATE with description:</strong> '$bind_string' (" . strlen($bind_string) . " chars)</li>\n";
echo "<li><strong>INSERT with description:</strong> '" . substr($bind_string, 0, -1) . "' (" . (strlen($bind_string)-1) . " chars) - without final 'i' for id</li>\n";
echo "<li><strong>UPDATE without description:</strong> '$bind_string_no_desc' (" . strlen($bind_string_no_desc) . " chars)</li>\n";
echo "<li><strong>INSERT without description:</strong> '" . substr($bind_string_no_desc, 0, -1) . "' (" . (strlen($bind_string_no_desc)-1) . " chars) - without final 'i' for id</li>\n";
echo "</ul>\n";
?>
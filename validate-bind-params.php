<?php
// Validate bind_param counts for packages queries

echo "<h2>Package Queries Parameter Validation</h2>\n";

// List all fields
$all_fields = ['title', 'description', 'price_label', 'price_value', 'price_unit', 'icon_class', 'features', 'featured', 'button_text', 'button_link', 'hotel', 'pesawat', 'price_quad', 'price_triple', 'price_double'];
$fields_without_description = ['title', 'price_label', 'price_value', 'price_unit', 'icon_class', 'features', 'featured', 'button_text', 'button_link', 'hotel', 'pesawat', 'price_quad', 'price_triple', 'price_double'];

echo "<h3>Field Analysis:</h3>\n";
echo "<p><strong>With description:</strong> " . count($all_fields) . " fields</p>\n";
echo "<p><strong>Without description:</strong> " . count($fields_without_description) . " fields</p>\n";

echo "<h3>Query Analysis:</h3>\n";

// UPDATE with description
echo "<h4>1. UPDATE with description:</h4>\n";
$update_with_desc = "UPDATE packages SET title=?, description=?, price_label=?, price_value=?, price_unit=?, icon_class=?, features=?, featured=?, button_text=?, button_link=?, hotel=?, pesawat=?, price_quad=?, price_triple=?, price_double=? WHERE id=?";
$placeholders_update_with_desc = substr_count($update_with_desc, '?');
$bind_param_update_with_desc = 'sssssssissssssi';
echo "<p>Placeholders: $placeholders_update_with_desc</p>\n";
echo "<p>bind_param string: '$bind_param_update_with_desc' (length: " . strlen($bind_param_update_with_desc) . ")</p>\n";
echo "<p>Parameters: 15 fields + 1 id = 16 total</p>\n";
echo "<p>Status: " . ($placeholders_update_with_desc == strlen($bind_param_update_with_desc) && $placeholders_update_with_desc == 16 ? "✅ CORRECT" : "❌ MISMATCH") . "</p>\n";

// INSERT with description
echo "<h4>2. INSERT with description:</h4>\n";
$insert_with_desc = "INSERT INTO packages(title, description, price_label, price_value, price_unit, icon_class, features, featured, button_text, button_link, hotel, pesawat, price_quad, price_triple, price_double) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
$placeholders_insert_with_desc = substr_count($insert_with_desc, '?');
$bind_param_insert_with_desc = 'sssssssisssssss';
echo "<p>Placeholders: $placeholders_insert_with_desc</p>\n";
echo "<p>bind_param string: '$bind_param_insert_with_desc' (length: " . strlen($bind_param_insert_with_desc) . ")</p>\n";
echo "<p>Parameters: 15 fields</p>\n";
echo "<p>Status: " . ($placeholders_insert_with_desc == strlen($bind_param_insert_with_desc) && $placeholders_insert_with_desc == 15 ? "✅ CORRECT" : "❌ MISMATCH") . "</p>\n";

// UPDATE without description
echo "<h4>3. UPDATE without description:</h4>\n";
$update_without_desc = "UPDATE packages SET title=?, price_label=?, price_value=?, price_unit=?, icon_class=?, features=?, featured=?, button_text=?, button_link=?, hotel=?, pesawat=?, price_quad=?, price_triple=?, price_double=? WHERE id=?";
$placeholders_update_without_desc = substr_count($update_without_desc, '?');
$bind_param_update_without_desc = 'ssssssissssssi';
echo "<p>Placeholders: $placeholders_update_without_desc</p>\n";
echo "<p>bind_param string: '$bind_param_update_without_desc' (length: " . strlen($bind_param_update_without_desc) . ")</p>\n";
echo "<p>Parameters: 14 fields + 1 id = 15 total</p>\n";
echo "<p>Status: " . ($placeholders_update_without_desc == strlen($bind_param_update_without_desc) && $placeholders_update_without_desc == 15 ? "✅ CORRECT" : "❌ MISMATCH") . "</p>\n";

// INSERT without description
echo "<h4>4. INSERT without description:</h4>\n";
$insert_without_desc = "INSERT INTO packages(title, price_label, price_value, price_unit, icon_class, features, featured, button_text, button_link, hotel, pesawat, price_quad, price_triple, price_double) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
$placeholders_insert_without_desc = substr_count($insert_without_desc, '?');
$bind_param_insert_without_desc = 'ssssssisssssss';
echo "<p>Placeholders: $placeholders_insert_without_desc</p>\n";
echo "<p>bind_param string: '$bind_param_insert_without_desc' (length: " . strlen($bind_param_insert_without_desc) . ")</p>\n";
echo "<p>Parameters: 14 fields</p>\n";
echo "<p>Status: " . ($placeholders_insert_without_desc == strlen($bind_param_insert_without_desc) && $placeholders_insert_without_desc == 14 ? "✅ CORRECT" : "❌ MISMATCH") . "</p>\n";

echo "<hr>\n";
echo "<h3>Data Types Mapping:</h3>\n";
echo "<ul>\n";
echo "<li><strong>s</strong> = string: title, description, price_label, price_value, price_unit, icon_class, features, button_text, button_link, hotel, pesawat, price_quad, price_triple, price_double</li>\n";
echo "<li><strong>i</strong> = integer: featured, id</li>\n";
echo "</ul>\n";

echo "<h3>Correct bind_param patterns:</h3>\n";
echo "<ul>\n";
echo "<li><strong>UPDATE with description:</strong> sssssssissssssi (16 chars)</li>\n";  
echo "<li><strong>INSERT with description:</strong> sssssssisssssss (15 chars)</li>\n";  
echo "<li><strong>UPDATE without description:</strong> ssssssissssssi (14 chars)</li>\n";  
echo "<li><strong>INSERT without description:</strong> ssssssisssssss (13 chars)</li>\n";  
echo "</ul>\n";
?>
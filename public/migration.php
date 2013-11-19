<?php

error_reporting(E_ALL);

/*
 * connect to mysql
 */
$productionMysql = new mysqli('romania-today.ru.wpdev', 'wpdev', 'ghbdtnghbdtn', 'romania-today');
$productionMysql->set_charset('cp1251');
$testMysql = new mysqli('localhost', 'root', 'root', 'romania-today');
$testMysql->set_charset('utf8');

/*
 * truncate new table
 */
$testMysql->query('truncate articles');


/*
 * Array with mappign for categories
 *
 * old id => array('id_category', 'id_subcategory')
 */
$mappign = array(
    '52'  => array('category_id' => '1', 'subcategory_id' => '2'),
    '50'  => array('category_id' => '1', 'subcategory_id' => '3'),
    '51' => array('category_id' => '1', 'subcategory_id' => '4'),
    '49' => array('category_id' => '1', 'subcategory_id' => '5'),
    '48' => array('category_id' => '1', 'subcategory_id' => '6'),
    '47' => array('category_id' => '1', 'subcategory_id' => '7'),
    '561' => array('category_id' => '8', 'subcategory_id' => '0'));


/*
 * Get all rows from production(old) database
 */
$result = $productionMysql->query("select parent_id, url, title, metatitle, keywords, description, create_date, modify_date, field_text, field_annotation, field_image from content");
while($row = mysqli_fetch_assoc($result)) {
    if( isset($mappign[$row['parent_id']]) ) {

        // iconv
        foreach($row as $key => $value) {
            $row[$key] = iconv('cp1251', 'utf-8//IGNORE', $value);
        }

        // change some fields
        $category_id = $mappign[$row['parent_id']]['category_id'];
        $subcategory_id = $mappign[$row['parent_id']]['subcategory_id'];
        $preview = mb_substr($row['field_image'], 1);
        $content = $testMysql->real_escape_string($row['field_text']);

        $testMysql->query('insert into articles ( category_id, subcategory_id, article_name, alias, meta_title, meta_description, description, content, preview, created_at, updated_at )
                           values( "'. $category_id .'", "'. $subcategory_id .'", "'. $row['title'] .'", "'. $row['url'] .'", "'. $row['metatitle'] .'", "'. $row['description'] .'", "'. $row['field_annotation'] .'", "'. $content .'", "'. $preview .'", "'. $row['create_date'] .'", "'. $row['modify_date'] .'" );');

//        if(isset($testMysql->error)) {
//            echo $testMysql->error;
//            exit;
//        }
    }
}


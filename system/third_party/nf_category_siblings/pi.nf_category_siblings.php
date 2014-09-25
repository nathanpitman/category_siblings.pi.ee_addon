<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
    'pi_name'           => 'Category Siblings',
    'pi_version'        => '1.0',
    'pi_author'         => 'Nathan Pitman',
    'pi_author_url'     => 'http://ninefour.co.uk/labs',
    'pi_description'    => 'Returns all category siblings',
    'pi_usage'          => Nf_category_siblings::usage()
);

/**
 * Nf_category_siblings Class
 *
 * @package         ExpressionEngine
 * @category        Plugin
 * @author          Nathan Pitman @ Nine Four Ltd
 * @copyright       Copyright (c) 20014 Nine Four Ltd.
 * @link            http://ninefour.co.uk/labs
 */

class Nf_category_siblings {

    var $return_data;

    /**
     * Constructor
     *
     */
    function Nf_category_siblings()
    {

        $entry_ids = "";
        $related_category_ids = "";

        if (!$category_ids = ee()->TMPL->fetch_param('category_ids')) {
            return;
        } else {

            $category_ids = explode('|', $category_ids);
            $category_ids_string = implode(',', $category_ids);

            // SELECT * FROM exp_category_posts WHERE cat_id IN ({current_category_ids}) GROUP BY entry_id
            ee()->db->select('entry_id');
            ee()->db->where_in('cat_id', $category_ids_string);
            ee()->db->group_by('entry_id');
            $entry_ids_object = ee()->db->get('category_posts');

            if ($entry_ids_object->num_rows()) {
                foreach ($entry_ids_object->result_array() AS $key=>$row) {
                    $entry_ids .= $row['entry_id'].",";
                }

                // SELECT cat_id FROM exp_category_posts WHERE entry_id IN ({entry_ids}) GROUP BY cat_id
                ee()->db->select('cat_id');
                ee()->db->where_in('entry_id', array($entry_ids));
                ee()->db->group_by('cat_id');
                $cat_ids_object = ee()->db->get('category_posts');

                if ($cat_ids_object->num_rows()) {
                    foreach ($cat_ids_object->result_array() AS $key=>$row) {
                        $related_category_ids .= $row['cat_id']."|";
                    }
                }

            }

        }

        $this->return_data = rtrim($related_category_ids,'|');
    }

    // --------------------------------------------------------------------

    /**
     * Usage
     *
     * Plugin Usage
     *
     * @access  public
     * @return  string
     */
    function usage()
    {
        ob_start();
        ?>
        Pass a pipe delimited list of category IDs and this plug-in will return related categories.

        {exp:nf_category_siblings category_ids="1|2|3"}

        would output

        "4|5|6"

        <?php
        $buffer = ob_get_contents();

        ob_end_clean();

        return $buffer;
    }

    // --------------------------------------------------------------------

}
// END CLASS

/* End of file pi.nf_category_siblings.php */
/* Location: ./system/expressionengine/third_party/nf_category_siblings/pi.nf_category_siblings.php */

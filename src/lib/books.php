<?php

namespace _PluginName;

class Book
{

    protected $bookDetailMetaBox;

    public function __construct()
    {
        add_action('init', array($this, 'register'));
        $this->bookDetailMetaBox = new BookDetailMetaBox();
    }

    public function register()
    {
        $labels = array(
            'name'               => __('Books', '_pluginname'),
            'menu_name'          => __('Books', '_pluginname'),
            'singular_name'      => __('Book', '_pluginname'),
        );
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
        );
        register_post_type('book', $args);
    }
}



class BookDetailMetaBox
{

    protected  $author = "_pluginname_book_author";
    protected  $published_date = "_pluginname_book_published_date";
    protected  $layout = "_pluginname_book_layout";
    protected  $nonce_action = "_pluginname_book_detail_metabox_update";
    protected  $nonce_field = "_pluginname_book_detail_metabox_nonce";

    public function __construct()
    {
        add_action('add_meta_boxes_book', array($this, '_pluginname_book_detail_metabox'));
        add_action('save_post', array($this, '_pluginname_book_detail_metabox_update'), 10, 2);
    }

    public function _pluginname_book_detail_metabox()
    {
        add_meta_box(
            '_pluginname_book_detail_metabox', // Unique ID
            'Book Details',           // Box title
            array($this, '_pluginname_book_detail_metabox_html'), // Callback function to render the content
            'book', // Post type
            'normal', // Context (normal, advanced, side)
            'high' // Priority (high, core, default, low)
        );
    }

    function _pluginname_book_detail_metabox_html($post)
    {
        /* echo "<pre>";
        var_dump(get_post_type_object($post->post_type));
        echo "</pre>"; */
        // Retrieve existing meta values for the book
        $author = get_post_meta($post->ID, $this->author, true);
        $published_date = get_post_meta($post->ID, $this->published_date, true);
        $layout = get_post_meta($post->ID, $this->layout, true);

        // Output the HTML for the meta box

        wp_nonce_field($this->nonce_action, $this->nonce_field);
?>
        <label for="<?php echo $this->author; ?>">Author:</label>
        <input class="widefat" type="text" id="<?php echo $this->author; ?>" name="<?php echo $this->author; ?>" value="<?php echo esc_attr($author); ?>" />

        <label for="<?php echo $this->published_date; ?>">Published Date:</label>
        <input class="widefat" type="text" id="<?php echo $this->published_date; ?>" name="<?php echo $this->published_date; ?>" value="<?php echo esc_attr($published_date); ?>" />

        <label for="<?php echo $this->layout; ?>">Layout:</label>
        <select class="widefat" name="<?php echo $this->layout; ?>" id="<?php echo $this->layout; ?>" value="<?php echo esc_attr($layout); ?>">
            <option <?php selected($layout, "full") ?> value="full">Full Width</option>
            <option <?php selected($layout, "sidebar") ?> value="sidebar">Post with Sidebar</option>
        </select>

<?php
    }

    public function _pluginname_book_detail_metabox_update($post_id, $post)
    {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!isset($_POST[$this->nonce_field]) || !wp_verify_nonce($_POST[$this->nonce_field], $this->nonce_action)) {
            return;
        }

        $edit_cap = get_post_type_object($post->post_type)->cap->edit_post;
        if (!current_user_can($edit_cap, $post_id)) {
            return;
        }

        // Save or update the meta values for the book
        $author = isset($_POST[$this->author]) ? sanitize_text_field($_POST[$this->author]) : '';
        $published_date = isset($_POST[$this->published_date]) ? sanitize_text_field($_POST[$this->published_date]) : '';
        $layout = isset($_POST[$this->layout]) ? sanitize_text_field($_POST[$this->layout]) : '';


        // Reason why we put '_' on this case is metakeys need to have underscore as prefix

        update_post_meta($post_id, $this->author, $author);
        update_post_meta($post_id, $this->published_date, $published_date);
        update_post_meta($post_id, $this->layout, $layout);
    }
}

(new \_PluginName\Book);

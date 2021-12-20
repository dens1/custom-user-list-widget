<?php
/*
Plugin Name: User List Widget
Description: Adds a widget with list of users.
Version: 1.0.0
Author: Den
Author URI:
*/

// Creating the widget
class ulp_widget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(

            'ulp_widget',

            __('User List', 'site_domain'),

            array('description' => __('Widget that shows all users and comment count', 'site_domain'),)
        );
    }

    // Creating widget front-end
    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        $number = $instance['number'];
        $show = $instance['show'];
        $no_comments = $instance['no-comments'];
        $role = $instance['role'];

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];

        $params = array(
            'number' => $number,
            'orderby' => 'comment_count',
            'role' => $role

        );

        $uq = new WP_User_Query($params);
        if (!empty($uq->results)) {

            foreach ($uq->results as $u) {
                $comment_array = get_comment_count($u->ID);
                $comment_total = '<span>(' . $comment_array['total_comments'] . ')</span>';
                $show_total_comments = $show ? ($comment_total) : '';
                $users_with_comments = $comment_array['total_comments'] > 0 ? $u->display_name . $show_total_comments : '';

                if ($no_comments) {
                    echo '<p>' . $u->display_name . $show_total_comments . '</p>';

                } else {

                    echo $users_with_comments;
                }


            }
        } else {
            echo 'No users registered on the site.';
        }


        echo $args['after_widget'];
    }

    // Widget Backend
    public function form($instance)
    {
        if (isset($instance['number'])) {
            $count = $instance['number'];
        }
        if (isset($instance['text'])) {
            $show = $instance['show'];
        }
        if (isset($instance['text'])) {
            $no_comments = $instance['no-comments'];
        }



        // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Users to show:'); ?></label>
            <input id="<?php echo $this->get_field_id('number'); ?>"
                   name="<?php echo $this->get_field_name('number'); ?>" type="number"
                   value="<?php echo esc_attr($count); ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('show'); ?>"><?php _e('Show comments count:'); ?></label>
            <input id="<?php echo $this->get_field_id('show'); ?>" name="<?php echo $this->get_field_name('show'); ?>"
                   type="checkbox" <?php checked($show); ?> />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('no-comments'); ?>"><?php _e('Show users with 0 comments:'); ?></label>
            <input id="<?php echo $this->get_field_id('no-comments'); ?>"
                   name="<?php echo $this->get_field_name('no-comments'); ?>"
                   type="checkbox"<?php checked($no_comments); ?> />
        </p>

            <?php
        }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['number'] = sanitize_text_field($new_instance['number']);
        $instance['show'] = !empty($new_instance['show']) ? 1 : 0;
        $instance['no-comments'] = !empty($new_instance['no-comments']) ? 1 : 0;
        $instance['role'] = !empty($new_instance['role']) ? 1 : 0;

        return $instance;
    }

}

// Register and load the widget
function wpb_load_widget()
{
    register_widget('ulp_widget');
}

add_action('widgets_init', 'wpb_load_widget');

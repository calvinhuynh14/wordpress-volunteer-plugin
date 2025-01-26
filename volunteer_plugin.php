<?php
/**
 * Plugin Name: Volunteer Opportunity Plugin
 * Description: A plugin to create, manage, and display volunteer opportunities.
 * Version: 1.0
 * Author: Calvin Huynh 000819227
 */

 
// Activation Hook - Creates database table
function volunteer_activation() {
    global $wpdb;

    // Generate the SQL query to create the table
    $wpdb->query ("CREATE TABLE wp_Opportunities(
                   OpportunityID INT AUTO_INCREMENT PRIMARY KEY,
                   Position VARCHAR(100) NOT NULL,
                   Organization VARCHAR(100) NOT NULL,
                   Type ENUM('one-time', 'recurring', 'seasonal') NOT NULL,
                   Email VARCHAR(100) NOT NULL,
                   Description TEXT NOT NULL,
                   Location VARCHAR(100) NOT NULL,
                   Hours INT NOT NULL,
                   Skills_required TEXT NOT NULL
                   );
                ");
    // Insert a sample opportunity
    $wpdb->query("INSERT INTO wp_Opportunities(Position, Organization, Type, Email, Description, Location, Hours, Skills_required)
                  VALUES('Painter', 'Charity Foundation', 'one-time', 'tester@gmail.com', 'Painting a house for my old uncle', '1234 Elm St', 4, 'Painting skills');
                ");
}
register_activation_hook(__FILE__, 'volunteer_activation');

// Deactivation Hook - Drops database table
function volunteer_deactivation() {
    global $wpdb;
    // Drop the table
    $wpdb->query("DROP TABLE IF EXIST Opportunities");
}
register_deactivation_hook(__FILE__, 'volunteer_deactivation');

function volunteer_enqueue_styles(){
    if ($hook !== 'volunteer-opportunity-plugin') {
        return;
    }

    wp_enqueue_style(
        'volunteer-admin-form-styles',
        plugin_url('assets/style_form.css', __FILE__),
        array(),
        '1.0',
        'all'
    );
}

add_action('admin_enqueue_scripts', 'volunteer_enqueue_styles');

// Add to the admin menu
add_action( 'admin_menu', 'volunteer_plugin_menu' );
function volunteer_plugin_menu() {
    add_menu_page(
        'Volunteer Opportunities',      // Page title
        'Volunteers',                   // Menu title (sidebar)
        'manage_options',               // Capability (who can access)
        'volunteer-opportunity-plugin', // Menu slug
        'volunteer_admin_page',         // Function to call
    );
}

function volunteer_admin_page() {
    ?>
    <h1>Volunteer Opportunities</h1>
    <form method="post">
        <!-- Organization Information Section -->
        <fieldset>
            <legend>Organization Information:</legend>

            <label for="organization">Organization:</label>
            <input 
                type="text" 
                id="organization" 
                name="organization" 
                maxlength="100" 
                placeholder="E.g. Spring Charity" 
                required
            >

            <label for="location">Location:</label>
            <input 
                type="text" 
                id="location" 
                name="location" 
                maxlength="100" 
                placeholder="E.g. 123 Main St, ON L5L E5E" 
                required
            >

            <label for="email">Email:</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                maxlength="100" 
                placeholder="E.g. johndoe@example.com" 
                required
            >
        </fieldset>

        <!-- Position Information Section -->
        <fieldset style='border: 1px solid #000000'>
            <legend>Position Information:</legend>

            <label for="position">Position:</label>
            <input 
                type="text" 
                id="position" 
                name="position" 
                maxlength="100" 
                placeholder="E.g. Painter" 
                required
            >

            <label for="hours">Hours:</label>
            <input 
                type="number" 
                id="hours" 
                name="hours" 
                min="1" 
                max="1000" 
                placeholder="E.g. 10" 
                required
            >

            <label for="type">Type:</label>
            <select id="type" name="type" required>
                <option value="one-time">One-time</option>
                <option value="recurring">Recurring</option>
                <option value="seasonal">Seasonal</option>
            </select>

            <label for="description">Description (500 chars.):</label>
            <textarea 
                id="description" 
                name="description" 
                maxlength="500" 
                placeholder="E.g. Help Spring Charity repaint their gymnasium." 
                required
            ></textarea>

            <label for="skills">Skills Required (500 chars.):</label>
            <textarea 
                id="skills" 
                name="skills" 
                maxlength="500" 
                placeholder="E.g. Teamwork, painting, communication, time management" 
                required
            ></textarea>
        </fieldset>

        <!-- Submit Button -->
        <div>
            <input type="submit" name="submit" value="Add Opportunity">
        </div>
    </form>


    <?php
}



?>
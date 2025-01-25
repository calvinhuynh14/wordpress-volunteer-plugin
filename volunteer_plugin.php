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
    <table>
        <form method='post'>
            <tr>
                <td>Position:</td>
                <td><input type='text' name='position' required></td>
                <td>Organization:</td>
                <td><input type='text' name='organization' required></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><input type='email' name='email' required></td>
                <td>Location:</td>
                <td><input type='text' name='location' required></td>
            </tr>
            <tr>
                <td>Hours:</td>
                <td><input type='number' name='hours' required></td>
                <td>Type:</td>
                <td>
                    <select name='type' required>
                        <option value='one-time'>One-time</option>
                        <option value='recurring'>Recurring</option>
                        <option value='seasonal'>Seasonal</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Description:</td>
                <td colspan='3'><textarea name='description' required></textarea></td>
            </tr>
            <tr>
                <td>Skills Required:</td>
                <td colspan='3'><textarea name='skills' required></textarea></td>
            </tr>
            <tr>
                <td colspan='4'><input type='submit' name='submit' value='Add Opportunity'></td>
            </tr>
        </form>
    </table>

    <?php

}



?>
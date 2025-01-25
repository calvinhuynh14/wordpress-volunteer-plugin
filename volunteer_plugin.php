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

    $wpdb->query("INSERT INTO wp_Opportunities(Position, Organization, Type, Email, Description, Location, Hours, Skills_required)
                  VALUES('Painter', 'Charity Foundation', 'one-time', 'tester@gmail.com', 'Painting a house for my old uncle', '1234 Elm St', 4, 'Painting skills');
                ");
}

register_activation_hook(__FILE__, 'volunteer_activation');

?>
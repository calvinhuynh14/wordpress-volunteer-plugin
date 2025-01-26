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
                  VALUES('Painter', 'Charity Foundation', 'one-time', 'tester@gmail.com', 'Painting a house for my old uncle', '1234 Elm St', 4, 'Painting, Teamwork, Collaboration');
                ");
    $wpdb->query("INSERT INTO wp_Opportunities(Position, Organization, Type, Email, Description, Location, Hours, Skills_required)
                    VALUES('Volunteer Knitter', 'Hamilton Public Library', 'recurring', 'tester@hpl.ca', 'Knit accessories for children to stay warm', '987 Main St', 6, 'Knitting, Communication, Empathy');
                    ");

    // Reset AUTO_INCREMENT to 1
    $wpdb->query("ALTER TABLE $table_name AUTO_INCREMENT = 1");
}
register_activation_hook(__FILE__, 'volunteer_activation');

// Deactivation Hook - Drops database table
function volunteer_deactivation() {
    global $wpdb;
    // Drop the table
    $wpdb->query("DROP TABLE IF EXISTS wp_Opportunities");
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
    <!-- Colour Palette
     Black: 1F1F1F
     Dark Blue: 1D3557
     Medium Blue: 457B9D
     Light Blue: A8DADC
     Off-White: F1FAEE
     Red Accent: E63946
    -->

    <h1 style="text-align: center; font-size: 3em; color: #1D3557; padding: 15px;">Volunteer Opportunities</h1>
    <form method="post" style="max-width: 1000px; margin: auto;">
        <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: space-between;">
            <!-- Organization Information Section -->
            <fieldset style="flex: 1; border: 2px solid #1D3557; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <legend style="font-weight: bold; font-size: 1.5em; color: #457B9D; padding: 0 5px;">Organization Information:</legend>

                <label for="organization" style="display: block; color: #457B9D; margin: 10px 0 5px; font-weight: bold;">Organization:</label>
                <input 
                    type="text" 
                    id="organization" 
                    name="organization" 
                    maxlength="100" 
                    placeholder="E.g. Spring Charity" 
                    style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #1F1F1F; border-radius: 4px;"
                    required
                >

                <label for="location" style="display: block; color: #457B9D; margin: 10px 0 5px; font-weight: bold;">Location:</label>
                <input 
                    type="text" 
                    id="location" 
                    name="location" 
                    maxlength="100" 
                    placeholder="E.g. 123 Main St, ON L5L E5E" 
                    style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #1F1F1F; border-radius: 4px;"
                    required
                >

                <label for="email" style="display: block; color: #457B9D; margin: 10px 0 5px; font-weight: bold;">Email:</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    maxlength="100" 
                    placeholder="E.g. johndoe@example.com" 
                    style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #1F1F1F; border-radius: 4px;"
                    required
                >
            </fieldset>

            <!-- Position Information Section -->
            <fieldset  style="flex: 1; border: 2px solid #1D3557; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <legend style="font-weight: bold; font-size: 1.5em; color: #457B9D; padding: 0 5px;">Position Information:</legend>

                <label for="position" style="display: block; color: #457B9D; margin: 10px 0 5px; font-weight: bold;">Position:</label>
                <input 
                    type="text" 
                    id="position" 
                    name="position" 
                    maxlength="100" 
                    placeholder="E.g. Painter" 
                    style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #1F1F1F; border-radius: 4px;"
                    required
                >

                <label for="hours" style="display: block; color: #457B9D; margin: 10px 0 5px; font-weight: bold;">Hours:</label>
                <input 
                    type="number" 
                    id="hours" 
                    name="hours" 
                    min="1" 
                    max="1000" 
                    placeholder="E.g. 10" 
                    style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #1F1F1F; border-radius: 4px;"
                    required
                >

                <label for="type" style="display: block; color: #457B9D; margin: 10px 0 5px; font-weight: bold;">Type:</label>
                <select id="type" name="type" required>
                    <option value="one-time">One-time</option>
                    <option value="recurring">Recurring</option>
                    <option value="seasonal">Seasonal</option>
                </select>

                <label for="description" style="display: block; color: #457B9D; margin: 10px 0 5px; font-weight: bold;">Description (500 chars.):</label>
                <textarea 
                    id="description" 
                    name="description" 
                    maxlength="500" 
                    placeholder="E.g. Help Spring Charity repaint their gymnasium." 
                    style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #1F1F1F; border-radius: 4px;"
                    required
                ></textarea>

                <label for="skills" style="display: block; color: #457B9D; margin: 10px 0 5px; font-weight: bold;">Skills Required (500 chars.):</label>
                <textarea 
                    id="skills" 
                    name="skills" 
                    maxlength="500" 
                    placeholder="E.g. Teamwork, painting, communication, time management" 
                    style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #1F1F1F; border-radius: 4px;"
                    required
                ></textarea>
            </fieldset>
        </div>

        <!-- Submit Button -->
        <div style="text-align: center;">
            <input type="submit" name="submit" value="Add Opportunity"style="padding: 10px 20px; background-color: #0073aa; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">
        </div>
    </form>


    <?php
}



?>
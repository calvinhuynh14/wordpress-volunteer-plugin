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
    // Insert a sample opportunities
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
// Admin page - Containing Table of positions and form to create new positions
function volunteer_admin_page() {
    global $wpdb;
    $table_name = "wp_Opportunities";

    // Store database entries for table
    $opportunities = $wpdb->get_results("SELECT * FROM $table_name");

    // Store opportunity to edit
    $edit_opportunity = null;

    // Handle Edit Button
    if( isset($_POST['edit'])) {
        $opportunity_id = intval($_POST['opportunity_id']);
        $editing_opportunity = $wpdb->get_row( $wpdb->prepare("SELECT * 
                                                                FROM $table_name 
                                                                WHERE OpportunityID = %d", $opportunity_id));
    }

    // Handle Update Button
    if ( isset($_POST['update'])){
        $opportunity_id = intval($_POST['opportunity_id']);

        $position = sanitize_text_field($_POST['position']);
        $hours = intval($_POST['hours']);
        $type = sanitize_text_field($_POST['type']);
        $description = sanitize_textarea_field($_POST['description']);
        $skills = sanitize_textarea_field($_POST['skills']);

        $organization = sanitize_text_field($_POST['organization']);  
        $location = sanitize_text_field($_POST['location']);
        $email = sanitize_email($_POST['email']);

        $updated = $wpdb->update(
            $table_name,
            array(
                'Position' => $position,
                'Hours' => $hours,
                'Type' => $type,
                'Description' => $description,
                'Skills_required' => $skills,
                'Organization' => $organization,
                'Location' => $location,
                'Email' => $email,
            ),
            array('OpportunityID' => $opportunity_id),
            array('%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s'),
            array('%d'),
        );

        if ($updated) {
            echo '<div class="updated"><p>Opportunity updated successfully!</p></div>';
        } else {
            echo '<div class="error"><p>Failed to update the opportunity. Please try again.</p></div>';
        }

    }

    // Handle Delete Button
    if (isset($_POST['delete'])){
        $opportunity_id = intval($_POST['opportunity_id']);

        $deleted = $wpdb->delete(
            $table_name,
            array('OpportunityID'=>$opportunity_id),
            array('%d'),
        );

        if($deleted) { 
            echo '<div class="updated"><p>Opportunity deleted successfully. Refresh page.</p></div>';
        } else {
            echo '<div class="error"><p>Failed to delete the opportunity.</p></div>';
        }
    }

    // Handle Add Button
    if (isset($_POST['submit'])){
        $position = sanitize_text_field($_POST['position']);
        $hours = intval($_POST['hours']);
        $type = sanitize_text_field($_POST['type']);
        $description = sanitize_textarea_field($_POST['description']);
        $skills = sanitize_textarea_field($_POST['skills']);

        $organization = sanitize_text_field($_POST['organization']);  
        $location = sanitize_text_field($_POST['location']);
        $email = sanitize_email($_POST['email']);

        $inserted = $wpdb->insert(
            $table_name,
            array(
                'Position'=>$position,
                'Hours'=>$hours,
                'Type'=>$type,
                'Description'=>$description,
                'Skills_required'=>$skills,
                'Organization'=>$organization,
                'Location'=>$location,
                'Email'=>$email,
            ),
            array('%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s')
        );

        if($inserted) { 
            echo '<div class="updated"><p>Opportunity added successfully. Refresh page.</p></div>';
        } else {
            echo '<div class="error"><p>Failed to add the opportunity.</p></div>';
        }
    }

    ?>
    <!-- Colour Palette
     Black: 1F1F1F
     Dark Blue: 1D3557
     Medium Blue: 457B9D
     Light Blue: A8DADC
     Off-White: F1FAEE
     Red Accent: E63946
    -->
    <h1 style="text-align: center; font-size: 3em; color: #1D3557; padding: 10px;">Volunteer Opportunities</h1>

    <!-- Opportunities Table -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 5%; border: 2px solid #1D3557;">
        <thead>
            <tr>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Position</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Organziation</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Type</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Email</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Description</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Location</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Hours</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Skills Required</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;"> Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($opportunities) {
                foreach ($opportunities as $opportunity){
                    echo "<tr>";
                    echo "<td style='border: 2px solid #1D3557; padding: 10px'>". esc_html($opportunity->Position) . "</td>";
                    echo "<td style='border: 2px solid #1D3557; padding: 10px'>". esc_html($opportunity->Organization) . "</td>";
                    echo "<td style='border: 2px solid #1D3557; padding: 10px'>". esc_html($opportunity->Type) . "</td>";
                    echo "<td style='border: 2px solid #1D3557; padding: 10px'>". esc_html($opportunity->Email) . "</td>";
                    echo "<td style='border: 2px solid #1D3557; padding: 10px'>". esc_html($opportunity->Description) . "</td>";
                    echo "<td style='border: 2px solid #1D3557; padding: 10px'>". esc_html($opportunity->Location) . "</td>";
                    echo "<td style='border: 2px solid #1D3557; padding: 10px'>". esc_html($opportunity->Hours) . "</td>";
                    echo "<td style='border: 2px solid #1D3557; padding: 10px'>". esc_html($opportunity->Skills_required) . "</td>";
                    echo "<td style='border: 1px solid #1F1F1F; padding: 10px; text-align: center;'>";
                    echo "<form method='post' style='display: inline;'>
                            <input type='hidden' name='opportunity_id' value='" . esc_attr($opportunity->OpportunityID) . "'>
                            <input type='submit' name='edit' value='Edit' style='color: white; background-color: #38b000; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;'>
                        </form>";
                    echo "<form method='post' style='display: inline;'>
                            <input type='hidden' name='opportunity_id' value='" . esc_attr($opportunity->OpportunityID) . "'>
                            <input type='submit' name='delete' value='Delete' style='color: white; background-color: #E63946; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;'>
                        </form>";
                    echo "</td>";

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='12' style='text-align: center; color: #E63946; font-weight: bold; font-size: 1.25em; padding: 10px;'>No opportunities found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Field Form -->
    <form method="post" style="max-width: 1000px; margin: auto;">
        <input type="hidden" name="opportunity_id" value="<?php echo $editing_opportunity ? esc_attr($editing_opportunity->OpportunityID) : ''; ?>">

        <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: space-between;">

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
                    value="<?php echo $editing_opportunity ? esc_attr($editing_opportunity->Position) : ''; ?>"  
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
                    value="<?php echo $editing_opportunity ? esc_attr($editing_opportunity->Hours) : ''; ?>" 
                    style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #1F1F1F; border-radius: 4px;"
                    required
                >

                <label for="type" style="display: block; color: #457B9D; margin: 10px 0 5px; font-weight: bold;">Type:</label>
                <select id="type" name="type" required>
                    <option value="one-time" <?php echo $editing_opportunity && $editing_opportunity->Type === 'one-time' ? 'selected' : ''; ?>>One-time</option>
                    <option value="recurring" <?php echo $editing_opportunity && $editing_opportunity->Type === 'recurring' ? 'selected' : ''; ?>>Recurring</option>
                    <option value="seasonal" <?php echo $editing_opportunity && $editing_opportunity->Type === 'seasonal' ? 'selected' : ''; ?>>Seasonal</option>
                </select>

                <label for="description" style="display: block; color: #457B9D; margin: 10px 0 5px; font-weight: bold;">Description (500 chars.):</label>
                <textarea 
                    id="description" 
                    name="description" 
                    maxlength="500" 
                    placeholder="E.g. Help Spring Charity repaint their gymnasium." 
                    style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #1F1F1F; border-radius: 4px;"
                    required
                ><?php echo $editing_opportunity ? esc_textarea($editing_opportunity->Description) : ''; ?></textarea>

                <label for="skills" style="display: block; color: #457B9D; margin: 10px 0 5px; font-weight: bold;">Skills Required (500 chars.):</label>
                <textarea 
                    id="skills" 
                    name="skills" 
                    maxlength="500" 
                    placeholder="E.g. Teamwork, painting, communication, time management" 
                    style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #1F1F1F; border-radius: 4px;"
                    required
                ><?php echo $editing_opportunity ? esc_textarea($editing_opportunity->Skills_required) : ''; ?></textarea>
            </fieldset>

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
                    value="<?php echo $editing_opportunity ? esc_attr($editing_opportunity->Organization) : ''; ?>" 
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
                    value="<?php echo $editing_opportunity ? esc_attr($editing_opportunity->Location) : ''; ?>" 
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
                    value="<?php echo $editing_opportunity ? esc_attr($editing_opportunity->Email) : ''; ?>" 
                    style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #1F1F1F; border-radius: 4px;"
                    required
                >
            </fieldset>
        </div>

        <!-- Submit Button -->
        <div style="text-align: center;">
            <input type="submit" name="<?php echo $editing_opportunity ? "update" : "submit"; ?>" value="<?php echo $editing_opportunity ? "Update Opportunity" : "Add Opportunity"; ?>" style="padding: 10px 20px; background-color: #457B9D; color: #F1FAEE; border: 1px solid #1D3557; border-radius: 4px; cursor: pointer; font-size: 16px;">

        </div>
    </form>

    <?php
};

add_shortcode('volunteer', 'volunteer_shortcode');
function volunteer_shortcode($atts) {
    global $wpdb;
    $table_name = 'wp_Opportunities';

    // Get data entries
    $opportunities = $wpdb->get_results("SELECT * FROM $table_name");

    $atts = shortcode_atts(
        array(
            'hours'=>null, 
            'type'=>null,
        ),
        $atts
    );

    ob_start();
    ?>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 5%; border: 2px solid #1D3557;">
        <thead>
            <tr>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Position</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Organziation</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Type</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Email</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Description</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Location</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Hours</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;">Skills Required</th>
                <th style="font-size: 1.25em; border: 2px solid #1D3557;"> Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($opportunities): ?>
                <?php foreach ($opportunities as $opportunity): ?>
                    <tr>
                        <td style="border: 2px solid #1D3557; padding: 10px"><?php echo esc_html($opportunity->Position); ?></td>
                        <td style="border: 2px solid #1D3557; padding: 10px"><?php echo esc_html($opportunity->Organization); ?></td>
                        <td style="border: 2px solid #1D3557; padding: 10px"><?php echo esc_html($opportunity->Type); ?></td>
                        <td style="border: 2px solid #1D3557; padding: 10px"><?php echo esc_html($opportunity->Email); ?></td>
                        <td style="border: 2px solid #1D3557; padding: 10px"><?php echo esc_html($opportunity->Description); ?></td>
                        <td style="border: 2px solid #1D3557; padding: 10px"><?php echo esc_html($opportunity->Location); ?></td>
                        <td style="border: 2px solid #1D3557; padding: 10px"><?php echo esc_html($opportunity->Hours); ?></td>
                        <td style="border: 2px solid #1D3557; padding: 10px"><?php echo esc_html($opportunity->Skills_required); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 10px; color: #E63946;">No opportunities found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php
        };
?>
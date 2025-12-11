# Cloud Gaming Availability - Usage Examples

## Shortcode Examples

### Basic Usage
Display a game with default settings:
```
[cloud_gaming_availability game_id="123"]
```

### With Custom Theme
Display with dark theme:
```
[cloud_gaming_availability game_id="123" theme="dark"]
```

Display with light theme:
```
[cloud_gaming_availability game_id="123" theme="light"]
```

Auto-detect system theme preference:
```
[cloud_gaming_availability game_id="123" theme="auto"]
```

### With Column Configuration
Display with 2 columns:
```
[cloud_gaming_availability game_id="123" columns="2"]
```

Display with 4 columns:
```
[cloud_gaming_availability game_id="123" columns="4"]
```

### Combining Parameters
```
[cloud_gaming_availability game_id="123" theme="dark" columns="3" show_description="true"]
```

Hide game description:
```
[cloud_gaming_availability game_id="123" show_description="false"]
```

## Template Functions

You can use these functions in your theme templates:

### Get a Game
```php
<?php
$game = get_post( 123 );
echo $game->post_title;
?>
```

### Display Game Shortcode
```php
<?php
echo do_shortcode( '[cloud_gaming_availability game_id="123"]' );
?>
```

### Get Platform Availability
```php
<?php
$availability = cga_get_game_availability( 123 );
foreach ( $availability as $platform_id ) {
    $platform = cga_get_platform( $platform_id );
    echo $platform['name'];
}
?>
```

### Check Specific Platform
```php
<?php
if ( cga_is_available_on_platform( 123, 'geforce-now' ) ) {
    echo 'Available on GeForce NOW!';
}
?>
```

### Get All Platforms
```php
<?php
$platforms = cga_get_platforms();
foreach ( $platforms as $platform_id => $platform ) {
    echo $platform['name'];
}
?>
```

### Get Platform Logo
```php
<?php
$logo = cga_get_platform_logo( 'geforce-now' );
if ( $logo ) {
    echo '<img src="' . esc_url( $logo ) . '" alt="GeForce NOW">';
}
?>
```

### Get Platform Statistics
```php
<?php
$stats = cga_get_platform_stats();
foreach ( $stats as $platform_id => $data ) {
    echo $data['name'] . ': ' . $data['count'] . ' games';
}
?>
```

### Get Games by Platform
```php
<?php
$games = cga_get_games_by_platform( 'geforce-now' );
if ( $games->have_posts() ) {
    while ( $games->have_posts() ) {
        $games->the_post();
        echo get_the_title();
    }
    wp_reset_postdata();
}
?>
```

### Count Games on Platform
```php
<?php
$count = cga_count_games_on_platform( 'geforce-now' );
echo 'Games available: ' . $count;
?>
```

## Page Templates

### Game Archive Page
Create a template to display all games:

```php
<?php
get_header();
?>
<div class="archive-games">
    <h1><?php post_type_archive_title(); ?></h1>
    
    <?php
    $games = cga_get_games( 12, 0 );
    
    if ( $games->have_posts() ) {
        echo '<div class="games-grid">';
        while ( $games->have_posts() ) {
            $games->the_post();
            ?>
            <div class="game-item">
                <h2><?php the_title(); ?></h2>
                <?php the_post_thumbnail( 'medium' ); ?>
                <?php
                echo do_shortcode( 
                    '[cloud_gaming_availability game_id="' . get_the_ID() . '" columns="3"]'
                );
                ?>
            </div>
            <?php
        }
        echo '</div>';
        wp_reset_postdata();
    }
    ?>
</div>
<?php
get_footer();
?>
```

### Platform-Specific Page
Display games available on a specific platform:

```php
<?php
get_header();
?>
<div class="platform-games">
    <h1>Games on GeForce NOW</h1>
    
    <?php
    $games = cga_get_games_by_platform( 'geforce-now', 20 );
    
    if ( $games->have_posts() ) {
        while ( $games->have_posts() ) {
            $games->the_post();
            ?>
            <div class="game-item">
                <h2><?php the_title(); ?></h2>
                <?php the_post_thumbnail( 'medium' ); ?>
                <p><?php the_excerpt(); ?></p>
            </div>
            <?php
        }
        wp_reset_postdata();
    } else {
        echo '<p>No games available on this platform.</p>';
    }
    ?>
</div>
<?php
get_footer();
?>
```

### Platform Statistics Dashboard
```php
<?php
get_header();
?>
<div class="platform-stats">
    <h1>Platform Statistics</h1>
    
    <?php
    $stats = cga_get_platform_stats();
    ?>
    <table class="stats-table">
        <thead>
            <tr>
                <th>Platform</th>
                <th>Games Available</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ( $stats as $platform_id => $data ) {
                ?>
                <tr>
                    <td><?php echo esc_html( $data['name'] ); ?></td>
                    <td><?php echo intval( $data['count'] ); ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>
<?php
get_footer();
?>
```

## Custom CSS Examples

### Override Button Color
```css
.cga-container {
    --cga-button-color: #ff5722;
}
```

### Increase Logo Size
```css
.cga-container {
    --cga-logo-size: 64px;
}
```

### Customize Spacing
```css
.cga-container {
    --cga-spacing: 24px;
    --cga-padding: 16px;
}
```

### Dark Theme Customization
```css
.cga-container.cga-theme-dark {
    --cga-button-color: #00bcd4;
    --cga-text-color: #e0e0e0;
}
```

## Admin Programmatic Usage

### Create a Game Programmatically
```php
<?php
$game_id = wp_insert_post( array(
    'post_type'    => 'cloud_games',
    'post_title'   => 'Cyberpunk 2077',
    'post_content' => 'An open-world action RPG...',
    'post_status'  => 'publish',
) );

// Set featured image (if you have an image)
set_post_thumbnail( $game_id, $attachment_id );

// Set platform availability
update_post_meta(
    $game_id,
    'cga_platform_availability',
    array( 'geforce-now', 'xbox-cloud-gaming', 'ps-plus-premium' )
);
?>
```

### Update Platform Availability
```php
<?php
update_post_meta(
    123,
    'cga_platform_availability',
    array( 'geforce-now', 'amazon-luna', 'shadow-pc' )
);
?>
```

### Get All Games with Availability Info
```php
<?php
$games = get_posts( array(
    'post_type'      => 'cloud_games',
    'posts_per_page' => -1,
) );

foreach ( $games as $game ) {
    $availability = cga_get_game_availability( $game->ID );
    echo $game->post_title . ' (' . count( $availability ) . ' platforms)';
}
?>
```

## Filters & Hooks

### Customize Platform Information
```php
<?php
add_filter( 'cga_platforms', function( $platforms ) {
    // Modify platforms array
    return $platforms;
} );
?>
```

### Custom Shortcode Processing
```php
<?php
add_filter( 'cga_shortcode_output', function( $output, $atts, $game ) {
    // Modify shortcode output
    return $output;
}, 10, 3 );
?>
```

### Game Query Arguments
```php
<?php
add_filter( 'cga_game_query_args', function( $args ) {
    // Modify the WP_Query arguments
    return $args;
} );
?>
```

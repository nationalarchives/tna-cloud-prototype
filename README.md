# tna-cloud-prototype

The National Archives Cloud plugin for WordPress

Renders a static HTML version of the WordPress Multisite

## 1.0 Development setup: WordPress nested Multisite

### 1.1 Create host for WordPress in MAMP Pro

Assuming you have MAMP Pro installed, under the ‘Hosts‘ tab create a new server, give it a name and within MAMP/htdocs/ create a directory that reflects the name of the server. Having done this click ‘Save‘.

### 1.2 1-click WordPress install in MAMP Pro

At this point we need make a decision on ports. WordPress Multisite will not work with MAMP’s default ports. You can set the ports for Apache and MySQL on the ‘General‘ tab.

Or you can change network.php after installing WordPress. Not recommended. Once installed, find the network.php inside wp-admin and change ```array( ':80', ':443' )``` to ```array( ':80', ':443', ':8888' )```.

Under the ‘Hosts‘ tab, with the new server selected, click ‘Extras‘ and install WordPress, providing a name of the blog and your email address as the email address.

### 1.3 Prepare WordPress

Login to the new WordPress installation. Under ‘Settings->Permalinks‘, select a Pretty Permalink.

Deactivate all active plugins.

### 1.4 Allow Multisite

Open wp-config.php and add this line above where it says ```/* That's all, stop editing! Happy blogging. */```.

```
/* Multisite */
define( 'WP_ALLOW_MULTISITE', true );
```

Refresh your browser.

### 1.5 Installing a Network

Go to ‘Tools->Network Setup‘. If given the option between sub-domains and sub-directories, choose sub-directories. Edit Network Title and Admin E-mail Address if you like. Click ‘Install‘.

### 1.6 Enabling the Network

If the ```.htaccess``` file doesn’t exist you need to create the file and AllowOveride All.

Follow the instructions on the Create a Network of WordPress Sites screen. We will edit the ```wp-config.php``` and ```.htaccess``` files again when creating a nested network.

### 1.7 Prepare Nested WordPress Multisite

Create the file ```sunrise.php``` inside wp-content.

Open ```wp-config.php``` and add this line above where it says ```/* That's all, stop editing! Happy blogging. */```.

```
// Activate sunrise script
define('SUNRISE', TRUE);
```

### 1.8 Edit sunrise.php

Open ```sunrise.php``` and add the follow code. Courtesy of Paul Underwood.

```
<?php
if( defined( 'DOMAIN_CURRENT_SITE' ) && defined( 'PATH_CURRENT_SITE' ) ) {
    if (!isset($current_site)) { 
        $current_site = new stdClass();
    }
    $current_site->id = (defined( 'SITE_ID_CURRENT_SITE' ) ? constant('SITE_ID_CURRENT_SITE') : 1);
    $current_site->domain = $domain = DOMAIN_CURRENT_SITE;
    $current_site->path  = $path = PATH_CURRENT_SITE;

    if( defined( 'BLOGID_CURRENT_SITE' ) )
        $current_site->blog_id = BLOGID_CURRENT_SITE;
 
    $url = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
 
    $patharray = (array) explode( '/', trim( $url, '/' ));
    $blogsearch = '';

    if( count( $patharray )){
        $pathsearch = '';
        foreach( $patharray as $pathpart ){
            $pathsearch .= '/'. $pathpart;
            $blogsearch .= $wpdb->prepare(" OR (domain = %s AND path = %s) ", $domain, $pathsearch .'/' );
        }
    }
 
    $current_blog = $wpdb->get_row( $wpdb->prepare("SELECT *, LENGTH( path ) as pathlen FROM $wpdb->blogs WHERE domain = %s AND path = '/'", $domain, $path) . $blogsearch .'ORDER BY pathlen DESC LIMIT 1');
 
    $blog_id = $current_blog->blog_id;
    $public  = $current_blog->public;
    $site_id = $current_blog->site_id;

    $current_site = pu_get_current_site_name( $current_site );
}
function pu_get_current_site_name( $current_site ) {
    global $wpdb;
    $current_site->site_name = wp_cache_get( $current_site->id . ':current_site_name', "site-options" );
    if ( !$current_site->site_name ) {
        $current_site->site_name = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->sitemeta WHERE site_id = %d AND meta_key = 'site_name'", $current_site->id ) );
        if( $current_site->site_name == null )
            $current_site->site_name = ucfirst( $current_site->domain );
        wp_cache_set( $current_site->id . ':current_site_name', $current_site->site_name, 'site-options');
    }
    return $current_site;
}
```

### 1.9 Edit .htaccess

Open ```.htaccess```

Find this line.

```
RewriteRule ^([_0-9a-zA-Z-]+/)?(wp-(content|admin|includes).*) $2 [L]
```

And replace it with this line.

```
RewriteRule ^(.+)?/(wp-(content|admin|includes)/.*) $2 [L]
```

Therefore the htaccess should look like this.

```
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]

# add a trailing slash to /wp-admin
RewriteRule ^([_0-9a-zA-Z-]+/)?wp-admin$ $1wp-admin/ [R=301,L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^(.+)?/(wp-(content|admin|includes)/.*) $2 [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(.*\.php)$ $2 [L]
RewriteRule . index.php [L]
```

### 1.10 Edit wp-config.php for WP Filesystem API (optional) 

Open wp-config.php and add this line, if it doesn't exist or experiencing update problems, above where it says ```/* That's all, stop editing! Happy blogging. */```.

```
define('FS_METHOD','direct');
```

### 1.11 And you’re done

Have a go creating nested sites.

Note: When you add a new site, the site address field will only allow lowercase letters (a-z) and numbers, so ‘/’ can’t be used. Create the site as normal with the end of path name in this field. Edit the site and you’ll be able to add the entire path to Path, Siteurl and Home fields.

#### References:

https://codex.wordpress.org/Create_A_Network

https://paulund.co.uk/wordpress-multisite-nested-paths

http://blog-en.mamp.info/2015/02/the-htaccess-file-and-mamp-pro.html


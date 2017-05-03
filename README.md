# tna-cloud-prototype

The National Archives Cloud plugin for WordPress

## 1.0 WordPress nested Multisite WordPress setup on a dev machine

### 1.1 Create host for WordPress in MAMP Pro

Assuming you have MAMP Pro installed, under the ‘Hosts‘ tab create a new server, give it a name and within MAMP/htdocs/ create a directory that reflects the name of the server. Having done this click ‘Save‘.

### 1.2 1-click WordPress install in MAMP Pro

At this point we need make a decision on ports. WordPress Multisite will not work with MAMP’s default ports. You can set the ports for Apache and MySQL on the ‘General‘ tab.

Or you can change network.php after installing WordPress. Not recommended. Once installed, find the network.php inside wp-admin and change array( ':80', ':443' ) to array( ':80', ':443', ':8888' ).

Under the ‘Hosts‘ tab, with the new server selected, click ‘Extras‘ and install WordPress, providing a name of the blog and your email address as the email address.

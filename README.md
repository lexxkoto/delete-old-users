# Delete Old Users Script

## Introduction

This is a PHP command line script that uses Moodle web services to find and delete old users from a Moodle site. Moodle includes UI tools to find and delete old users, but they can often time out or run out of memory if you try to delete thousands of users at once. It's really tedious to click the delete button over and over again, selecting 50 users at a time. This script automates the process using Moodle's web services.

To run this script, you will need:

* A Moodle site with Web Services enabled, and a web service and token set up for the script to use. Instructions are below.
* An administrator account that will be used to run the script. It's best to use a dedicated manual account rather than a real person's account.
* A CSV of the users you want to delete. You can easily generate this using Moodle's Bulk User Actions page.
* A computer with the command-line PHP installed. On Linux, you usually want to install the `php-cli` package. On MacOS, you can use [HomeBrew](https://brew.sh/) to install PHP. This script was tested in PHP 8 but any recent version should work.

## Configuring Your Site

First, you want to enable web services. Log in to Moodle as an administrator, and go to **Site Administration**. Under the  **General** tab, click **Advanced Features** and check the **Enable web services** box.

Now, we need to set up a web service that the script can use to talk to Moodle. On the **Server** tab, click **External Services**. This will show a list of any web services you have already set up.

Click the **Add** button at the bottom of the page. Give your new web service a useful name and shortname - I usually use something like "Old User Deletion" and "old_users". Make sure the **Enabled** and **Authorised users only** boxes are checked, then click **Add service**.

Now, you'll see a page that lets us choose which web service functions our new service is allowed to use. Click the **Add Functions** link, then use the search box to add this function:

* `core_user_delete_users`

Once you're done, click the **Add Functions** button.

Now we have set up our web service, we'll give our admin account access to use the web service.

Click the **Server** tab, and go to **External Services** again. Click the **Authorised Users** link. Use the search box to find your user, and add them to the list of authorised users.

Now our user has permission to use the web service, we'll generate a token for them. A token is used instead of a password to call web services, so it's important to keep your token a secret.

Click the **Server** tab at the top of the page, then click **Manage Tokens**. Click the **Create token** button at the top of the page. Use the search box to find your user, then make sure your service is chosen in the next box. If you want, you can set an expiry date on the token or limit it to certain IPs. This can be useful for security. Click the **Save changes** button, and your token will be shown in the list of tokens.

## Getting a List of Users

This script works by going through a CSV of courses. If you have database access, you can run your own query against `mdl_user` to find the users you want to delete.

If you don't have database access, you can use Moodle's Bulk User Actions tool to get a list of users to delete.

Log in to Moodle and go to **Site Administration**. On the **Users** tab, click **Bulk User Actions**. You can use filters to choose which users to include using the filter box at the top of the page. If you click **Show More**, you will see more options to filter by.

For example, you could add filters to show only the users who haven't logged into Moodle for five years, or who have no active enrolments in any course.

Click the **Add Filter** button to apply your filters. The box at the bottom of the page will update to show the users that match the filters you have chosen.

To download a CSV of these users, click the **Add all** button at the bottom of the page. Then, click the **With selected users** drop-down box and choose **Download**. Click the **Go** button to download a CSV of users.

## Preparing the Script

When you download the script, you will get three PHP files. `rest.php` and `functions.php` just contain behind-the-scenes code that helps the script talk to Moodle, and you shouldn't need to touch them.

The important code is in `delete-old-users.php`. Open it in a text editor.

At the top of the file, you'll find a few settings you can change. Below these settings, you'll find the code that actually finds, checks and deletes the users.

At the top of the file, you'll find space to put your Moodle site address and the token you generated earlier:

```
$config['site']     = 'https://moodle.something.ac.uk';
$config['token']    = '74833e911605ee374986da0e41874371';
```

There are two other config options you can set - whether you want the script to continue or stop if it encounters an error, and whether you want to enable test mode. When test mode is enabled, the script will tell you which users it wants to delete, but it won't actually delete them.

## Setting up the  CSV

Get your CSV of users, and put it in the same folder as the PHP scripts. Make sure it is called `users.php`.

Now, we need to tell our script which fields of the CSV it is interested in. Inside the script, you will find these settings:

```
$userIDField = 0;
$usernameField = 1;
$firstnameField = 3;
$lastnameField = 4;
```

These settings tell the script which fields of the CSV file contains the user's ID number, username, firstname and lastname. Note that the numbers start with 0 and not 1. So if your user ID is the fifth field of your CSV file, you should put 4 here, not 5.

## Running the Script

Now, we're ready to run the script. If you're running it for the first time, you should make sure ``$config['test']`` is set to ``true``. This will make the script tell you which users it wants to delete, but it won't actually delete them.

Open a terminal and change to the folder where your scripts are located. Type this to run the script:

``php delete-old-users.php``
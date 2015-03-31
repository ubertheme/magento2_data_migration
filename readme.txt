############ Following steps to work with UB Migrate Data Tool: ################

IMPORTANT NOTE: This Tool was tested and compatible with Magento 1.9.x and Magento 2 0.42.0 - beta 6, 7, 8, 9, 10, 11

# Step1: Install a Magento 2 website
    + Download the latest version of Magento2 at https://github.com/magento/magento2/releases
    + Install Magento 2 by guide at http://devdocs.magento.com/guides/v1.0/install-gde/bk-install-guide.html

# Step2: Clone source code of Tool, make and config Databases for this Tool
    1 - Make a folder with named "magento2-data-migration" in your web root ( example: PATH_TO_YOUR_WEB_ROOT_FOLDER\magento2-data-migration)
and clone all source code of this Tool from Git repository at https://bitbucket.org/joomsolutions/magento2_data_migration.git to folder you have just created.
        + Create a folder with named 'assets' in folder path PATH_TO_YOUR_WEB_ROOT_FOLDER\magento2-data-migration\
        + Create a folder with named 'runtime' in folder path PATH_TO_YOUR_WEB_ROOT_FOLDER\magento2-data-migration\protected\
        and make write able for 'assets' and 'runtime' folders.

    2 - This Tool as a Website App, so we need to make a database for this Tool.
        + Open your MySQL manager (example: phpMyAdmin ...) and create a database called 'magento2_data_migration'
        and then import the file SQL in PATH_TO_YOUR_WEB_ROOT_FOLDER\magento2-data-migration\protected\data\ub_tool.sql to this Database.

    3 - Open the config file at PATH_TO_YOUR_WEB_ROOT_FOLDER\magento2-data-migration\protected\config\config.php
    and find to line with comment text "//Database of tool" and put the Database information which you have just created in above step.
    In this step you need focus to params: host, dbname, tablePrefix, username, password, ...

    4 - Config the Database information of your website with Magento1:
        Open the config file at PATH_TO_YOUR_WEB_ROOT_FOLDER\magento2-data-migration\protected\config\config.php
        and find to comment text "//Database of Magento1" and put the correct Database information of Magento 1 website as above step.

    5 - Config the Database information of your website with Magento2:
        Open the config file at PATH_TO_YOUR_WEB_ROOT_FOLDER\magento2-data-migration\protected\config\config.php
and find to comment text "//Database of Magento2" and put the correct your Database information of Magento2 website as above step.

# Step 3: Run this Tool in Browser to migrate data
    1 - Open your browser and type the url to run this Tool. 
Example: http://localhost/magento2-data-migration/
and press Enter key.
    2 - Follow step by step to end step to migrate needed Data from Magento1 website to Magento2 website.

# Step4: Do some task bellow to finish migration data.
    1 - Re-save all Attribute Sets (Product Templates) migrated in the back-end of your Magento2 website. (Open the attribute set, edit if needed and click save button)
    2 - Open the command line window and go to the folder:
    PATH_TO_YOUR_WEB_ROOT_FOLDER\your_magento2_folder\dev\shell\
    and type command line: php indexer.php reindexall
    and press enter key to re-index all data in your Magento2 website.
    3 - To migrate needed media files, copy the folder at PATH_TO_YOUR_WEB_ROOT_FOLDER\your_magento1_folder\media\catalog
    and paste replace to
    PATH_TO_YOUR_WEB_ROOT_FOLDER\your_magento2_folder\pub\media\
    4 (optional) - To migrate needed downloadable files, copy the folder at PATH_TO_YOUR_WEB_ROOT_FOLDER\your_magento1_folder\media\downloadable
        and paste replace to
        PATH_TO_YOUR_WEB_ROOT_FOLDER\your_magento2_folder\pub\media\

# Step5: Now you can test the data migrated in your Magento2 website from browser.

################# GOOD LUCK!!!  ##########################
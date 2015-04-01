<strong>Magento 2</strong> beta version has just been released not long ago with lots of huge improvements compared to the previous versions.

Moving to the latest version is obviously beneficial for most Magento users.

However, the data migration may be troublesome for new users. In an attempt to help you with this, <strong>UberTheme</strong> is developing a handy migration tool to migrate data from old Magento version to Magento 2. Let’s see what we’ve got so far.

Here is what we’ve learnt about <strong>Magento 2</strong> beta’s new structure so far.

<!--more-->
<h3>Key changes in the database structures.</h3>
<div class="center">
<p align="center"><img src="http://joomlart.s3.amazonaws.com/images/userguide/jm_tips/migrationData/magento-1.jpg?v=20150401144700" alt="Magento 2 Migration Data Tool" /></p>

</div>
<h3>Changes in tables.</h3>
<h4>+ Name change for some tables</h4>
<div class="center">
<p align="center"><img src="http://joomlart.s3.amazonaws.com/images/userguide/jm_tips/migrationData/changes-in-tables.jpg?v=20150401144700" alt="Magento 2 Migration Data Tool" /></p>

</div>
<h4>+ Change of Attributes</h4>
<div class="center">
<p align="center"><img src="http://joomlart.s3.amazonaws.com/images/userguide/jm_tips/migrationData/attributes.jpg?v=20150401144700" alt="Magento 2 Migration Data Tool" /></p>

</div>
<h2>Our plan for the Magento 2 Migration tool</h2>
This upcoming <strong>Magento 2</strong> migration tool will help to transfer your database from older versions to the latest <strong>Magento 2</strong> in some simple steps. Our aim is to make it the most handy as possible.
<h4>Here are what the tool will help to migrate.</h4>
<div class="center">
<p align="center"><img src="http://joomlart.s3.amazonaws.com/images/userguide/jm_tips/migrationData/list2.jpg?v=20150401144700" alt="Magento 2 Migration Data Tool" /></p>

</div>
In this post, we are standing at process 1

To use this migration tool, follow the steps below.
<h4># Step1: Install Magento 2</h4>
+ Download the latest version of Magento2 from Github

+ Follow our <a href="http://www.ubertheme.com/magento-news/magento-2-0-installation-guide/">Installation guide</a> to Install Magento 2
<h4># Step2: Configure the tool</h4>
1 - Make a folder named "migrate_data_tool" in your web root. (For example: PATH_TO_YOUR_WEB_ROOT_FOLDER\migrate-data-tool) and clone all source code of this tool from Git repository at: https://github.com/ubertheme/magento2_data_migration to the folder you have just created.
<div class="center">
<p align="center"><img src="http://joomlart.s3.amazonaws.com/images/userguide/jm_tips/migrationData/migrate.jpg" alt="Magento 2 Migration Data Tool" /></p>

</div>
+ Create a folder named 'assets' in the folder path <strong>PATH_TO_YOUR_WEB_ROOT_FOLDER\migrate-data-tool </strong>
<div class="center">
<p align="center"><img src="http://joomlart.s3.amazonaws.com/images/userguide/jm_tips/migrationData/assets.jpg?v=20150401144700" alt="Magento 2 Migration Data Tool" /></p>

</div>
+ Create a folder named 'runtime' in the folder path <strong>PATH_TO_YOUR_WEB_ROOT_FOLDER\migrate-data-tool\protected </strong> and make it writeable for 'assets' and 'runtime' folders.
<div class="center">
<p align="center"><img src="http://joomlart.s3.amazonaws.com/images/userguide/jm_tips/migrationData/run-time.jpg?v=20150401144700" alt="Magento 2 Migration Data Tool" /></p>

</div>
2 - Our Magento data migration tool works as a website app, so we need to make a database for it.

+ Open your MySQL manager (for example: phpMyAdmin ...) and create a database called <strong>ub_migrate_data</strong> then import the file SQL in <strong>PATH_TO_YOUR_WEB_ROOT_FOLDER\migrate-data-tool\protected\data\ub_tool.sql</strong> to this Database.
<div class="center">
<p align="center"><img src="http://joomlart.s3.amazonaws.com/images/userguide/jm_tips/migrationData/ub-tool.jpg?v=20150401144700" alt="Magento 2 Migration Data Tool" /></p>

</div>
3 - Open the config file at <strong>PATH_TO_YOUR_WEB_ROOT_FOLDER\migrate-data-tool\protected\config\config.php</strong>
and find the line with comment text "//Database of tool" and put the Database information which you have just created in the above step.
In this step you need to focus on these params: host, dbname, username, password, etc.
<div class="center">
<p align="center"><img src="http://joomlart.s3.amazonaws.com/images/userguide/jm_tips/migrationData/2.jpg?v=20150401144700" alt="Magento 2 Migration Data Tool" /></p>

</div>
4 - Configure the Database information of your website with Magento 1:

Open the config file at <strong>PATH_TO_YOUR_WEB_ROOT_FOLDER\migrate-data-tool\protected\config\config.php</strong>
and navigate to comment text "//Database of Magento1" and put the correct Database information of Magento 1 website as the above step.
<div class="center">
<p align="center"><img src="http://joomlart.s3.amazonaws.com/images/userguide/jm_tips/migrationData/1.jpg?v=20150401144700" alt="Magento 2 Migration Data Tool" /></p>

</div>
5 - Configure the Database information of your website with Magento2:

Open the config file at <strong>PATH_TO_YOUR_WEB_ROOT_FOLDER\migrate-data-tool\protected\config\config.php</strong>
and navigate to comment text "//Database of Magento2" and put the correct your Database information of Magento2 website as above step.
<div class="center">
<p align="center"><img src="http://joomlart.s3.amazonaws.com/images/userguide/jm_tips/migrationData/11.jpg?v=20150401144700" alt="Magento 2 Migration Data Tool" /></p>

</div>
<h4># Step 3: Run this tool in your browser to migrate your data</h4>
1 - Open your browser and type in the url to run this tool.

For example: go to http://localhost/migrate-data-tool/ and press Enter key.

2 - Follow step by step to migrate needed Data from Magento 1 website to Magento2 website.
<div class="center">
<p align="center"><img src="http://joomlart.s3.amazonaws.com/images/userguide/jm_tips/migrationData/step-2.jpg?v=20150401144700" alt="Magento 2 Migration Data Tool" /></p>

</div>
<h4># Step 4: Complete the tasks below to finish the data migration process.</h4>
Re-save all the Attribute Sets (Product Template) migrated in the back-end of your Magento 2 website. (Open the attribute set, edit it if needed and click the save button)

Open the command line window and go to the folder:
<strong> PATH_TO_YOUR_WEB_ROOT_FOLDER\your_magento2_folder\dev\shell </strong>
and type in the command line: php indexer.php reindexall
then press enter key to re-index all data in your Magento 2 website.
<div class="center">
<p align="center"><img src="http://joomlart.s3.amazonaws.com/images/userguide/jm_tips/migrationData/img-2.jpg?v=20150401144700" alt="Magento 2 Migration Data Tool" /></p>

</div>
<h3>Copy media files to complete migration:</h3>
+ Copy the folder at PATH_TO_YOUR_WEB_ROOT_FOLDER\your_magento1_folder\media\catalog and paste replace to PATH_TO_YOUR_WEB_ROOT_FOLDER\your_magento2_folder\pub\media\

+ Copy the folder at PATH_TO_YOUR_WEB_ROOT_FOLDER\your_magento1_folder\media\downloadable and paste replace to PATH_TO_YOUR_WEB_ROOT_FOLDER\your_magento2_folder\pub\media\
<h4># Step 5: Now you can test the data which have been migrated into your Magento 2 website from the browser.</h4>

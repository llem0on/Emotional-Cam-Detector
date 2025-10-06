README.txt  
===========

Project Name: EmoCam
Developer: 888

Description:
------------
This project is a web application designed to assist psychologists during consultations with patients. The system includes an AI-based emotion recognition feature that analyzes patient emotions throughout the consultation session. It helps psychologists better understand the emotional states of their patients, session by session.

The application is built using HTML, CSS, JavaScript, PHP, and MySQL, with XAMPP as the local server environment. It allows psychologists to manage patients, conduct consultation sessions, and view emotional data collected during those sessions.

Contents:
---------
- All website files (PHP, HTML, CSS, JS, etc.)
- 'emocam_db.sql' – SQL export of the MySQL database used by this application.

How to Set Up the Project:
--------------------------

1. Install XAMPP (if not already installed):
   - Download from https://www.apachefriends.org/
   - Install and launch the XAMPP Control Panel.
   - Start Apache and MySQL and use MySQL as the admin.

2. Import the database:
   - Open your browser and go to: http://localhost/phpmyadmin
   - Click "Import"
   - Choose the file 'emocam_db.sql' (included in this folder)
   - Click "Go" to import the database

3. Set up the website files:
   - Copy the entire project folder (this folder) into 'C:\xampp\htdocs\'
   - Example: 'C:\xampp\htdocs\emocam'

4. Access the website:
   - In your browser, go to: http://localhost/emocam/signup.php (start page)

Notes to deploy the website:
------
- Make sure Apache and MySQL are running before accessing the website.
- Make sure to run this file with Python 3.11 with these following Python libraries already installed:
  1. flask
  2. flask-cors
  3. opencv-python
  4. numpy
  5. tensorflow
  6. Pillow

- Before starting the meeting, make sure to run the AI before hand
  Steps:
  1. Open this file in your device's Command Prompt (type 'cmd' in this file's directory)
  2. Run this command in the terminal
     C:\Users\(location of your Python 311)\python.exe fer_api.py
     e.g. C:\Users\zepha\AppData\Local\Programs\Python\Python311\python.exe fer_api.py

Steps to Launch the Website
Navigate to the Project Directory:

Open your terminal and change into the vulnerable_version directory:


cd /path/to/CS437-Vulnerable_Website-main/vulnerable_version

Start the Docker Containers:

Run the following command to launch the Apache server and MySQL database:

docker-compose up


Alternatively:

docker compose up


Access the Website:

Once the containers are running, open your browser and go to:

http://localhost:8080

Access the MySQL Database:

Open another terminal and execute the following command to connect to the MySQL database:


docker compose exec db mysql -u myuser -pmypass myappdb


This connects you to the MySQL database running in the db container.

Inspect the Database:

After logging into MySQL, you can view the database information:

SELECT * FROM users;

This will display the users table from the database.

<img width="1728" alt="Screenshot 2024-12-27 at 2 13 17 PM" src="https://github.com/user-attachments/assets/21246985-47dd-4c71-bf66-8ca9006aa858" />

<img width="1728" alt="Screenshot 2024-12-27 at 2 14 03 PM" src="https://github.com/user-attachments/assets/6072203c-d2fd-4901-af3a-6a6adc9771de" />

<img width="1728" alt="Screenshot 2024-12-27 at 2 14 11 PM" src="https://github.com/user-attachments/assets/682534b1-789f-46cf-80f3-be5783b71c6e" />

<img width="1728" alt="Screenshot 2024-12-27 at 2 14 22 PM" src="https://github.com/user-attachments/assets/0c7efe9c-1da3-4e49-ad78-d97079dfef52" />


Vulnerabilities Report (TXT Format)


Vulnerability 1: Reflected XSS (Cross-Site Scripting) 

Category: A7:2017 - Cross-Site Scripting (XSS) --> A03: 2021 - Injection

Technical Explanation: Reflected XSS allows an attacker to inject and execute malicious JavaScript in a victim's browser by crafting a special URL. When the user visits that URL, the JavaScript executes in their browser. This vulnerability appears in index.php where the search input parameter is directly included in the output HTML without sanitization.

Exploit:
There is a search bar in the index.php in which input did not sanitized correctly using "htmlspecialchars()". Also the search parameter is accepted through URL as well. Exploitation would be an attacker luring into victims clicking links such as "http://localhost:8080/index.php?search=<script>alert('XSS');</script>" which would run a javacsript code inside victims' browser.

Impact:
* Theft of sensitive user information like cookies or session tokens.
* Phishing or account hijacking.

Payload Used: "http://localhost:8080/index.php?search=<script>alert('XSS');</script>

------------------------------------------------------------------------------------------------

Vulnerability 2: Blind OS Command Injection (Login)

Category: A03: 2021 - Injection

Technical Explanation: Blind OS Command Injection refers to a server executing commands based on user input where output isn't directly displayed. In login.php, it would be possible for an attacker to escape from the text input and directly execute shell command without any kind of validation. In order to Show that execution of codes in the background happenning we pinged and chcked from wireshark.

Exploit:
While inside the "username" field attacker can escape using ";" and execute any kind of Shell commands which would run in the background of the server. An example for that would be "username": "newuser; rm -rf something", "password": "anything". This payload would let attacker delete server directories and files.

Impact:
* Execution of arbitrary commands on the server.
* Potential denial of service or system compromise.

Payload Used: 
"uername": test; ping 8.8.8.8
"password": "anything"

------------------------------------------------------------------------------------------------

Vulnerability 3: Blind OS Command Injection (Registration)

Category: A03: 2021 - Injection
Technical Explanation: Similar to the login vulnerability, this occurs in register.php where the username input is appended to the nslookup command without validation. Again the attacker is able to excape from the input parameters and execute Shell commands. Also we exemined this vulnerability by using wireshark.

Exploit:
While inside the "username" field attacker can escape using ";" and execute any kind of Shell commands which would run in the background of the server. An example for that would be "username": "newuser; cat something", "password": "anything". This payload would let attacker see file contents inside the server.
* Input: test; cat snap/var/passwd.txt (just an example guess)
* Result: The server executes the cat command, exposing sensitive files like /etc/passwd.

Impact:
* Information disclosure.
* Further exploitation of server vulnerabilities.
payload Used:
"uername": newuser; ping 8.8.8.8
"password": "anything"

------------------------------------------------------------------------------------------------

Vulnerability 4: CWE-352: Cross-Site Request Forgery (CSRF)

Category: A1:2021 - Broken Access Control

Technical Explanation: CSRF allows an attacker to trick a logged-in user's browser to send malicious requests to the application. For this project, the password update form was found to not include a CSRF token in update_password.php, which subjects it to unauthorized actions. The reason for this vulnerability Works is that while victim is logged in to the vulnerable website web browser automatically remember the session token and includes it in any kind of request sent to the website. So if attacker manages to trick the victim sending a http post request which would change the password the vulnerable website will accept as it came from the victim. We demonstrated this by creating an html which stored a post request to update_password.php.

Exploit:
* Visit a malicious page hosting an malicious code in somekind of way are able to send http request to change the password (in our case html had the malicious code) while victim is still logged in.
* Result: The password is changed without the user's consent.

Impact:
* Loss of account control.
* Unauthorized actions performed on behalf of the user.

Payload Used: A hidden form in csrf_exploit.html targeting update_password.php.

------------------------------------------------------------------------------------------------

Vulnerability 5: Server-Side Request Forgery (SSRF)

Category: A10:2021 - Server-Side Request Forgery
Technical Explanation: SSRF vulnerabilities allow an attacker to make the server request internal or protected resources. This is seen in ssrf.php where the application uses a blacklist to block certain strings which is not enough to cover all the malicious sites all over the internet. Thus attacker has many ways of attacks, first attacker is able to download malicious files from internet over /ftp or /file protocols, moreover, attacker is able to perform DOS attacks by asking for URL that stores vast content (meta-data), lastly they can perform attacks using this vulnerable website as a host which would protect their identity.

Payload Used:
(Since we had no malicious server to download malicious file from we only performed Dos attack)
Inside URL we put "http://169.254.169.254/latest/meta-data/" which resulted in denail of server.

------------------------------------------------------------------------------------------------

Vulnerability 6: Path Traversal

Category: A5: 2021 - Security Misconfiguration
Technical Explanation: Path Traversal allows attackers to access files outside the intended directory by manipulating file paths. This vulnerability appears in path_traversal.php, where no checks are implemented for ../ sequences. Attacker can freely view the contents of all directories and files inside the server.

Impact:
* Disclosure of system-critical information.
* Unauthorized file access.

Payload Used:
../index.php
../login.php
------------------------------------------------------------------------------------------------

Vulnerability 7: Unrestricted File Upload

Category: A5: 2021 - Security Misconfiguration

Technical Explanation: The application allows files to be uploaded without validating their type or content. This appears in upload_vulnerable.php, where no restrictions on file extensions or contents exist. The file extention should have been checked, because attacker can upload malicious files such as executables which would hurt the server.

Exploit:
* Upload a .php web shell file.
* Access the uploaded file to execute arbitrary commands.
Impact:
* Full server compromise.
* Arbitrary code execution.
Payload Used: A .php file containing a web shell.



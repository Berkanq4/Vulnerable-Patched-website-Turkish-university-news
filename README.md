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

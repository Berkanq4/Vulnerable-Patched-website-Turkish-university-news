  In order to launch this website on your localhost port 8080, you requeire docker and docker-compose to be working correct please check them using "docker version",
and "docker-compose version" / "docker compose version" (this might be changing according to your set up).

  Afterwards fo into vulnerable_version and run the "docker compose up" or "docker-compose up" this will set up the apache server on your local host port 8080.
Also simultaniously it will run the dockerized version of mysql at the same time.

  Be aware that vulnerable version has, vulnerabilities and do not deploy it.


Clear Steps in order to launch correctly:
  1. cd into vulnerable_version
  2. run "docker-compose up" or "docker compose up"
  3. run "docker compose exec db mysql -u myuser -pmypass myappdb" in another terminal if you want to see open mysql database
  4. use "SELECT * FROM users;" to see the database information

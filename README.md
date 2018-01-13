Sample Micro CMS 
==================================

##What is this project?

This project is a small example of how to build a micro cms in an environment provisioned with docker with the following functional requirements:
* User can register post only when logged in
* The user logged into the system can view, edit or remove a post.
* The system should display posts in descending order
* Posts should be exposed in a REST API

##Dependencies

  * Docke:  https://docs.docker.com/engine/installation
  * Docker compose: https://docs.docker.com/compose/install

##How to run
First you must set your environment variables, to do this create an .env file

At the root of the project run: `docker-compose up`

**Atention:** Logical ports 8080 and 3306 must not be allocated.

##Containers

* Server
    * PHP 7.0
    * Apache
    * IP - Parametrized in .env file
    * Port - Parametrized in .env file
* MYSQL
    * MYSQL 8
    * IP - Parametrized in .env file
    * Port - Parametrized in .env file
* Memcached
    * Memcached 1.5.2
    * IP - Parametrized in .env file
* Composer PHP
    * composer

**Atention:** Configured private VPC in range Parametrized in .env file
    
##Docker compose comands
  * To `start` containers `[background]`: `docker-compose up -d`
  * To `start` containers `[foreground]`: `docker-compose up`. 
  * To `stop` container: `docker-compose $CONTAINERID stop`
  * To `view` container logs: `docker-compose logs`
  * To `acess` container: `docker exec -it $CONTAINERID bash`
  
**Atention:** You must be at the root of the project.

## Structure of the project

* bin 
    * doctrine reverse engineering
    * See http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/configuration.html
* docker
    * Customization of containers
* docs
    * project design artifacts
* resource
    * resource common of project
* src
    * source of project
* web
    * contains bootstrap application
    
# Application


The application was created using `MVC` and exposed in a `RESTFULL API`.

To access the features of the application, use the admin user (user: admin, passwd: admin)

To use the API, the authentication key (authentication) and value (Basic ZFhObGNpMXRhV055YnkxamJYTXRNakF4Tnc9PTpjR0Z6YzNkdmNtUXRiV2xqY204dFkyMXpMVEl3TVRjPT0=) in the header of the call

API Endpoints:
HTTP VERB - DESC - URL

[GET] - Get list with 11 elements: http://localhost:{PORT}/api/posts?limit=11&offSet=0

[POST] - Create element: http://localhost:{PORT}/api/posts

[GET] - Get element: http://localhost:{PORT}/api/posts/{ID}

[DETELE] - Get element: http://localhost:{PORT}/api/posts/{ID}

[PUT] - Get element: http://localhost:{PORT}/api/posts/{ID}

**Atention:** Posts should be accessed via API.

**Atention:** When registering a path, inform only the URI without the first /. 

**Atention:** All registered paths must be accessed by the API, with /api/pathCustom

APPLICATION:
http://localhost:{PORT}/login/
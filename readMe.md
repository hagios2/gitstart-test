NB: I made use of a makefile to simplify the commands to be running and also make the readMe file concise

#### API Documentation
https://documenter.getpostman.com/view/6535328/2sA3duEsnx


### Setup 

###### 1. git clone https://github.com/hagios2/gitstart-test.git
###### 2. cd gitstart-test
###### 3. make build

-------------------------------------------

### Kindly follow the instructions to tests the API endpoint

###### 1. make bash
###### 2. make create_db
###### 3. make migrate
###### 4. make seed
###### 5. make jwt_token
###### 6. the application should be up and running on 8080 of your localhost: http://127.0.0.1:8080
###### 7. click the API documentation link above to view the endpoints 
###### 8. use email: hagioswilson@gmail.com and password: password to login as erc


### Kindly follow the instructions to run unit tests

###### 1. make bash
###### 2. make create_test_db
###### 3. make migrate_test_db
###### 4. make seed_test_db
###### 5. make jwt_token
###### 6. bin/phpunit

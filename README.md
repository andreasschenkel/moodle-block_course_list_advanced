# moodle-block_course_list_advanced
The normal block course list shows all courses and has no information about the role in the course.

The block moodle-block_course_list_advanced adds some more information about a course. I adds the information which of the following roles a user is enrolled into a course.

For performance and testing-reason this first version only shows the content if the block is added into a course AND the user is trainer.

![image](https://user-images.githubusercontent.com/31856043/139007404-9f72772a-6e79-4d07-8fd0-4924613c47ac.png)

## numbers ##
### 1, 2: ###
counts the courses with the role trainer or student


### 3, 4, 5: ###
Also colors indicates courses that 
  (are in progress --> green), 
  (are past -> red),
  (are in the future --> blue)

### 6, 7, 8:
Startdate and enddate of a course

### 9:
able to delete a course if capability moodle/course:delete 

### 10:
Indicates trainer or student

### 11:
note jet supported


# Configure the block #

![image](https://user-images.githubusercontent.com/31856043/139282339-eebfd9bc-fbe5-430d-98e8-0435aef41a48.png)



At the end of the block a list of all courses is shown where the usere is enrolled as teacher.

# ToDo #
- choose better colors
- add some more languagestring to instead of hardcoded text
- optimize code for enrollmentcheck
- change from php to moodle-codestyle
- correct some spelling mistake
- block may be "expensive" in large moodleinstances with many users. instead of using a block it might be a good idea to implement the functionality as a part of the profile-page



# Changelog #

## [[v2.0.x]] ##

- added languagestring for capability

## [[v2.0.5]] ##
- changes to moodle coding styleguide 


## [[v2.0.4]] ##
- configurable max courses if is_siteadmin

## [[v2.0.3]] ##
- add list of all moodle courses is is_siteadmin
- some refactoring
- first very small unit tests


## [[v2.02]] ##
- add config to be able to add block to frontpage  
- add config to be able to add block to mypage
- some merged corrections in languagefile


## [[v2.01]] ##
only correcting the tag for the merge and release

## [[v2.00]] ##
- added some docúmentation
- added configuration
- only show content if block is in a course AND user is trainer
- added delete-icon (can be activated or deactivated) 


## [[v0.92]] ##
- Adding startdate and enddate.
- Also colors indicates courses that (are in progress --> green), (are past -> red), (are in the future --> blue)

## [[v0.91]] ##
added letter after the coursename to indicate role

## [[v0.9]] ##
beta-version with splited lists for teacher, student and nonediting teachers

## [[v0.8]] ##
beta-version for testing

## [[inital commit]] ##
initial commit on master-branch developed on moodle 3.9.9 

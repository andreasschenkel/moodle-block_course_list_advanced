# moodle-block_course_list_advanced
The normal block course list shows all courses and has no information about the role in the course.

The block moodle-block_course_list_advanced adds some more information about a course. I adds the information whitch of the following roles a user is enrolled into a course.
Also colors indicates courses that 
  are in progress --> green
  are past -> red
  are in the future --> blue

![grafik](https://user-images.githubusercontent.com/31856043/132042960-dc6645ea-7c34-4b5e-99fa-e4bcac4511c9.png)


At the end of the block a list of all courses is shown where the usere is enrolled as teacher.

# ToDo #
block may be "expensive" in large moodleinstances with many users. instead of using a block it might be a good idea to implement the functionality as a part of the profile-page

# Changelog #

## [[v0.92]] ##
Adding startdate and enddate.
Also colors indicates courses that 
  are in progress --> green
  are past -> red
  are in the future --> blue

## [[v0.91]] ##
added letter after the coursename to indicate role

## [[v0.9]] ##
beta-version with splited lists for teacher, student and nonediting teachers

## [[v0.8]] ##
beta-version for testing

## [[inital commit]] ##
initial commit on master-branch developed on moodle 3.9.9 

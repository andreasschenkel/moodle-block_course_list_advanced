# moodle-block_course_list_advanced
In this example the user is enrolled in two courses. The normal block course list shows all courses and has no information about the role in the course.

The block moodle-block_course_list_advanced adds after a course the information if a user is a teacher in this course. As a normal student no extra information is added.

![grafik](https://user-images.githubusercontent.com/31856043/131224531-ba8f91b4-ba9f-4cf0-be56-17273e0ba000.png)

At the end of the block a list of all courses is shown where the usere is enrolled as teacher.

# ToDo #
block may be "expensive" in large moodleinstances with many users. instead of using a block it might be a good idea to implement the functionality as a part of the profile-page

# Changelog #

## [[v0.91]] ##
added letter after the coursename to indicate role

## [[v0.9]] ##
beta-version with splited lists for teacher, student and nonediting teachers

## [[v0.8]] ##
beta-version for testing

## [[inital commit]] ##
initial commit on master-branch developed on moodle 3.9.9 

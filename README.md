# moodle-block_course_list_advanced
In this example the user is enrolled in two courses. The normal block course list shows all courses and has no information about the role in the course.

The block moodle-block_course_list_advanced adds after a course the information if a user is a teacher in this course. As a normal student no extra information is added.



![grafik](https://user-images.githubusercontent.com/31856043/128760132-60a4b7b4-02c2-40f7-8325-47b75b630611.png)


At the end of the block a list of all courses is shown where the usere is enrolled as teacher.

ToDo:
- Add languagestrings instead of hard coded strings
- find and implement an alternative to $editingteachers = get_users_by_capability($coursecontext, 'moodle/course:manageactivities'); 
- add a list of courses enrolled with the role "student"
- add a list of courses enrolled with the role "student"


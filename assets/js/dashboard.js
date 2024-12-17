// coded by Tim Galyanov

document.addEventListener('DOMContentLoaded', function () {
    const semesterRadios = document.querySelectorAll('input[name="semester"]');
    const courseSearchInput = document.getElementById('courseSearchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    const selectedCourseContainer = document.getElementById('selectedCourseContainer');
    const selectedCourseText = document.getElementById('selectedCourseText');
    const removeSelectedCourse = document.getElementById('removeSelectedCourse');
    const confirmAddCourseButton = document.getElementById('confirmAddCourse');
    const downloadPdfButton = document.getElementById('downloadPdfButton'); 
    const enrolledCoursesList = document.getElementById('enrolledCoursesList');
    const enrolledCoursesLoading = document.getElementById('enrolledCoursesLoading');

    let selectedSemester = 'Fall'; 
    let searchTimeout = null;
    let selectedCourse = null;
    let currentTimeout = null;
    let isNotificationVisible = false;
    

    function loadUsrSched() {
        fetch(`../api/get_user_schedule.php?semester=${encodeURIComponent(selectedSemester)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(offerings => {
                clrSchedTable();
                if (!offerings.error) {
                    offerings.forEach(o => {
                        populCourseBlock(o.section_code, o.code, o.day_of_week, o.start_time, o.end_time, o.location);
                    });
                } else {
                    console.error("Error fetching schedule:", offerings.error);
                    displayNotification("Error fetching schedule: " + offerings.error, 'error');
                }
            })
            .catch(err => {
                console.error('Error loading user schedule:', err);
                displayNotification('Error loading your schedule. Please try again.', 'error');
            });
    }


    function clrSchedTable() {
        document.querySelectorAll('.course-block').forEach(block => block.remove());
    }

  
    function populCourseBlock(sectionCode, courseCode, day, startTime, endTime, location) {
        const startHour = parseInt(startTime.split(':')[0], 10);
        const endHour = parseInt(endTime.split(':')[0], 10);
        const duration = endHour - startHour;
        for (let i = 0; i < duration; i++) {
            const currentHour = startHour + i;
            const cellClass = `${day}-${currentHour}`;
            const cell = document.querySelector(`.${cellClass}`);
            if (cell) {
                const courseBlockContainer = cell.querySelector('.course-block-container');
                const courseBlock = document.createElement('div');
                courseBlock.classList.add('course-block');
                courseBlock.setAttribute('data-section-code', sectionCode);
                courseBlock.setAttribute('data-course-code', courseCode);
                const courseInfo = document.createElement('div');
                courseInfo.innerHTML = `<strong>${courseCode}</strong> - ${location}<br>`;
        



                courseBlock.appendChild(courseInfo);
        
                courseBlockContainer.appendChild(courseBlock);
            }
        }
    }


    semesterRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            selectedSemester = radio.value;
            loadUsrSched();
            loadEnrolledCourses();
            if (selectedCourse) {
                deleteSelected();
            }
        });
    });
    

    courseSearchInput.addEventListener('keyup', function (e) {
        const query = courseSearchInput.value.trim();
        if (query.length >= 2) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetch(`../api/get_course_offerings.php?query=${encodeURIComponent(query)}&semester=${encodeURIComponent(selectedSemester)}`)
                    .then(res => res.json())
                    .then(data => {
                        searchSuggestions.innerHTML = '';
                        if (data.success && data.courses.length > 0) {
                            showCourseSuggestions(data.courses);
                        } else {
                            showNoSuggestions(data.error || 'No courses found.');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching courses:', error);
                        showNoSuggestions('Error fetching courses. Please try again.');
                    });
            }, 300);
        } else {
            searchSuggestions.innerHTML = '';
            searchSuggestions.style.display = 'none';
        }
    });


    function showCourseSuggestions(courses) {
        searchSuggestions.innerHTML = '';
        courses.forEach(course => {
            const suggestionItem = document.createElement('button');
            suggestionItem.type = 'button';
            suggestionItem.className = 'list-group-item list-group-item-action suggestion-item';
            suggestionItem.textContent = `${course.code} - ${course.name}`;
            suggestionItem.setAttribute('data-section-code', course.section_code); 
            suggestionItem.addEventListener('click', () => {
                chooseCourse(course);
            });
            searchSuggestions.appendChild(suggestionItem);
        });
        searchSuggestions.style.display = 'block';
    }


    function showNoSuggestions(message) {
        searchSuggestions.innerHTML = `<div class="list-group-item list-group-item-action disabled">${message}</div>`;
        searchSuggestions.style.display = 'block';
    }


    function chooseCourse(course) {
        selectedCourse = {
            code: course.code,
            section_code: course.section_code,
            name: course.name  
        };
        selectedCourseText.textContent = `${course.code} - ${course.name}`; 
        selectedCourseContainer.style.display = 'block';

        courseSearchInput.value = `${course.code} - ${course.name}`; 
        courseSearchInput.disabled = true;
        semesterRadios.forEach(radio => {
            radio.disabled = true;
        });

        searchSuggestions.innerHTML = '';
        searchSuggestions.style.display = 'none';
    }
  

    function deleteSelected() {
        selectedCourse = null;
        selectedCourseText.textContent = '';
        selectedCourseContainer.style.display = 'none';

        courseSearchInput.value = '';
        courseSearchInput.disabled = false;
        semesterRadios.forEach(radio => {
            radio.disabled = false;
        });
    }


    removeSelectedCourse.addEventListener('click', function () {
        deleteSelected();
        displayNotification('Selected course removed.', 'info');
    });

  
    confirmAddCourseButton.addEventListener('click', function () {
        if (!selectedCourse || !selectedCourse.section_code) {
            displayNotification('No course selected to add.', 'warning');
            return;
        }

        confirmAddCourseButton.disabled = true;
        const enrollmentLoading = document.getElementById('enrollmentLoading');
        if (enrollmentLoading) {
            enrollmentLoading.style.display = 'inline-block';
        }

        fetch(`../api/add_course_to_schedule.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                section_code: selectedCourse.section_code,
                semester: selectedSemester,
                csrf_token: csrfToken
            })
        })
        
            .then(res => res.json())
            .then(data => {
                
                confirmAddCourseButton.disabled = false;
                if (enrollmentLoading) {
                    enrollmentLoading.style.display = 'none';
                }

                if (data.success) {
                    displayNotification(`Course ${selectedCourse.code} added successfully!`, 'success');
                    loadUsrSched();
                    loadEnrolledCourses(); 
                    deleteSelected();
            
                    if (data.warning) {
                        displayNotification(data.warning, 'warning');
                    }
                } else {
                    displayNotification(data.error || 'Failed to add course.', 'error');
                }
            })
            
            .catch(err => {
                confirmAddCourseButton.disabled = false;
                const enrollmentLoading = document.getElementById('enrollmentLoading');
                if (enrollmentLoading) {
                    enrollmentLoading.style.display = 'none';
                }

                console.error('Error adding course:', err);
                displayNotification('Error adding course. Please try again.', 'error');
            });
    });

 
    function rmEnrolledCourse(courseCode, enrolledCourseElement) {
        if (!confirm('Are you sure you want to remove this course from your enrolled list?')) {
            return;
        }

        fetch(`../api/delete_course_by_code.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                course_code: courseCode,
                csrf_token: csrfToken 
            })
        })

            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    enrolledCourseElement.classList.add('fade-out');
                    enrolledCourseElement.addEventListener('animationend', function () {
                        enrolledCourseElement.remove();
                    });
                    displayNotification('Course removed successfully.', 'success');
                    loadUsrSched();
                    loadEnrolledCourses();
                } else {
                    displayNotification(data.error || 'Failed to remove the course.', 'error');
                }
            })

            .catch(error => {
                console.error('Error deleting course:', error);
                displayNotification('An error occurred while removing the course. Please try again.', 'error');
            });
    }

  
    function downloadPDF() {
        const scheduleTable = document.getElementById('scheduleTable');
        if (!scheduleTable) {
            displayNotification('Schedule table not found.', 'error');
            return;
        }

        html2canvas(scheduleTable, { scale: 2 }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const {jsPDF } = window.jspdf; 
            const pdf = new jsPDF('p', 'mm', 'a4');
            const imgWidth = 210; 
            const pageHeight = pdf.internal.pageSize.getHeight();
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            let heightLeft = imgHeight;
            let position = 0;

            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft > 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            pdf.save('my_schedule.pdf');
            displayNotification('Schedule downloaded as PDF.', 'success');
        }).catch(err => {
            console.error('Error generating PDF:', err);
            displayNotification('Failed to generate PDF. Please try again.', 'error');
        });
    }

  
    function loadEnrolledCourses() {
        enrolledCoursesList.innerHTML = '';
        enrolledCoursesLoading.style.display = 'block';

        fetch(`../api/get_enrolled_courses.php?semester=${encodeURIComponent(selectedSemester)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(courses => {
                enrolledCoursesLoading.style.display = 'none';
                showEnrolledCourses(courses);
            })
            .catch(err => {
                enrolledCoursesLoading.style.display = 'none';
                console.error('Error fetching enrolled courses:', err);
                displayNotification('Error fetching enrolled courses. Please try again.', 'error');
            });
    }

    
    function showEnrolledCourses(courses) {
        enrolledCoursesList.innerHTML = '';
        if (courses.length === 0) {
            enrolledCoursesList.innerHTML = '<p>No enrolled courses for this semester.</p>';
            return;
        }
    
        courses.forEach(course => {
            const enrolledCourseDiv = document.createElement('div');
            enrolledCourseDiv.classList.add('enrolled-course');
    
            const courseInfoDiv = document.createElement('div');
            courseInfoDiv.classList.add('course-info');
            courseInfoDiv.innerHTML = `
                <strong>${course.code}</strong><br>
                ${course.name}<br>
                <small>Prof. ${course.professor}</small>
            `;
    
            const deleteBtn = document.createElement('button');
            deleteBtn.classList.add('delete-enrolled-course-btn');
            deleteBtn.setAttribute('aria-label', 'Remove Enrolled Course');
            deleteBtn.innerHTML = '&times;';
            deleteBtn.addEventListener('click', function () {
                rmEnrolledCourse(course.code, enrolledCourseDiv);
            });
    
            enrolledCourseDiv.appendChild(courseInfoDiv);
            enrolledCourseDiv.appendChild(deleteBtn);
    
            enrolledCoursesList.appendChild(enrolledCourseDiv);
        });
    }


    function displayNotification(message, type = 'success') {
        if (currentTimeout) {
            clearTimeout(currentTimeout);
            currentTimeout = null;
        }
    
        const notification = document.getElementById('notification');
        const notificationText = document.getElementById('notificationText');
        const notificationClose = document.getElementById('notificationClose');
    

        if (notification.classList.contains('hide')) {
            notification.addEventListener('animationend', function handler() {
                notification.removeEventListener('animationend', handler);
                showNewNotification();
            }, { once: true });
        } else {
            showNewNotification();
        }
    
        function showNewNotification() {
            notification.classList.remove('success', 'error', 'warning', 'info', 'hide');
            
            notification.classList.add(type);
            notification.classList.add('show');
            notificationText.textContent = message;
    
            currentTimeout = setTimeout(() => {
                hideNotification();
            }, 5000);
    
            isNotificationVisible = true;
        }
    
        notificationClose.onclick = () => {
            if (currentTimeout) {
                clearTimeout(currentTimeout);
                currentTimeout = null;
            }
            hideNotification();
        };
    }
    
    function hideNotification() {
        const notification = document.getElementById('notification');
        
        if (!isNotificationVisible) return;
    
        notification.classList.add('hide');
        isNotificationVisible = false;
        
        if (currentTimeout) {
            clearTimeout(currentTimeout);
            currentTimeout = null;
        }
    
        notification.addEventListener('animationend', function handler() {
            notification.removeEventListener('animationend', handler);
            notification.classList.remove('show', 'hide');
        }, { once: true });
    }
            
   
    loadUsrSched();
    loadEnrolledCourses();
    
  
    if (downloadPdfButton) {
        downloadPdfButton.addEventListener('click', downloadPDF);
    } else {
        console.error('Download PDF button not found.');
    }
});

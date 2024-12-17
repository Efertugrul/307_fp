// coded by Efe
document.addEventListener('DOMContentLoaded', function () {
    const friendsList = document.getElementById('friendsList');
    const pendingReceivedList = document.getElementById('pendingReceivedList');
    const sendFriendInput = document.getElementById('sendFriendInput');
    const sendFriendButton = document.getElementById('sendFriendButton');
    const sendFriendFeedback = document.getElementById('sendFriendFeedback');
    let currentTimeout = null;
    let isNotificationVisible = false;

    const viewScheduleModal = new bootstrap.Modal(document.getElementById('viewScheduleModal'), {
        keyboard: false
    });
    const friendSemesterSelect = document.getElementById('friendSemesterSelect');
    const friendScheduleList = document.getElementById('friendScheduleList');

    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'), {
        keyboard: false
    });
    const confirmationModalLabel = document.getElementById('confirmationModalLabel');
    const confirmationModalBody = document.getElementById('confirmationModalBody');
    const confirmActionButton = document.getElementById('confirmActionButton');
    let selectedAction = null; 
    let selectedFriendData = {}; 
    let selectedFriendId = null;



    function getFriendsAndRequests() {
        fetch('../api/get_friends.php') 
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateFriendsList(data.friends);
                    populatePendingFriendsList(data.pending_received);
                } else {
                    displayNotification(data.error || 'Failed to fetch friends.', 'error');
                }
            })
            .catch(err => {
                displayNotification('Error fetching friends. Please try again.', 'error');
            });
    }

    function populateFriendsList(friends) {
        friendsList.innerHTML = '';
        if (friends.length === 0) {
            friendsList.innerHTML = '<li class="list-group-item">You have no friends yet.</li>';
            return;
        }

        friends.forEach(friend => {
            const listItem = document.createElement('li');
            listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
            listItem.textContent = friend.name;

            const buttonsDiv = document.createElement('div');

            const viewButton = document.createElement('button');
            viewButton.className = 'btn btn-sm btn-info me-2';
            viewButton.textContent = 'View Schedule';
            viewButton.addEventListener('click', () => {
                viewFriendSchedule(friend.id, friend.name);
            });

            const removeButton = document.createElement('button');
            removeButton.className = 'btn btn-sm btn-danger';
            removeButton.textContent = 'Remove Friend';
            removeButton.addEventListener('click', () => {
                selectedAction = 'removeFriend';
                selectedFriendData = { id: friend.id, name: friend.name };
                confirmationModalLabel.textContent = 'Confirm Removal';
                confirmationModalBody.textContent = `Are you sure you want to remove ${friend.name} from your friends?`;
                confirmActionButton.textContent = 'Remove';
                confirmActionButton.classList.remove('btn-primary');
                confirmActionButton.classList.add('btn-danger');
                confirmationModal.show();
            });

            buttonsDiv.appendChild(viewButton);
            buttonsDiv.appendChild(removeButton);
            listItem.appendChild(buttonsDiv);

            friendsList.appendChild(listItem);
        });
    }

    function populatePendingFriendsList(pending) {
        pendingReceivedList.innerHTML = '';
        if (pending.length === 0) {
            pendingReceivedList.innerHTML = '<li class="list-group-item">No pending friend requests.</li>';
            return;
        }

        pending.forEach(request => {
            const listItem = document.createElement('li');
            listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
            listItem.textContent = request.name;

            const buttonsDiv = document.createElement('div');

            const acceptButton = document.createElement('button');
            acceptButton.className = 'btn btn-sm btn-success me-2';
            acceptButton.textContent = 'Accept';
            acceptButton.addEventListener('click', () => {
                selectedAction = 'acceptFriendRequest';
                selectedFriendData = { id: request.id, name: request.name };
                confirmationModalLabel.textContent = 'Confirm Acceptance';
                confirmationModalBody.textContent = `Do you want to accept the friend request from ${request.name}?`;
                confirmActionButton.textContent = 'Accept';
                confirmActionButton.classList.remove('btn-danger');
                confirmActionButton.classList.add('btn-primary');
                confirmationModal.show();
            });

            buttonsDiv.appendChild(acceptButton);
            listItem.appendChild(buttonsDiv);
            pendingReceivedList.appendChild(listItem);
        });
    }

    function sendFriendRequest() {
        const receiverUsername = sendFriendInput.value.trim();

        if (receiverUsername === '') {
            displayNotification('Please enter an email to send a friend request.', 'warning');
            return;
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(receiverUsername)) {
            displayNotification('Please enter a valid email address.', 'warning');
            return;
        }

        sendFriendButton.disabled = true;
        sendFriendFeedback.innerHTML = '';

        fetch('../api/send_friend_request.php', { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ receiver_username: receiverUsername })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    displayNotification(data.message, 'success');
                    sendFriendInput.value = '';
                    getFriendsAndRequests();
                } else {
                    displayNotification(data.error || 'Failed to send friend request.', 'error');
                }
            })
            .catch(err => {
                displayNotification('Error sending friend request. Please try again.', 'error');
            })
            .finally(() => {
                sendFriendButton.disabled = false;
            });
            
    }

    function viewFriendSchedule(friendId, friendName) {
        selectedFriendId = friendId;
        document.getElementById('viewScheduleModalLabel').textContent = `${friendName}'s Schedule`;
        friendSemesterSelect.value = '';
        friendScheduleList.innerHTML = '';
        viewScheduleModal.show();
    }





    function getFriendSchedule() {
        const semester = friendSemesterSelect.value;
        if (semester === '') {
            displayNotification('Please select a semester to view the schedule.', 'warning');
            return;
        }
        document.getElementById('scheduleLoading').style.display = 'block';
        friendScheduleList.innerHTML = '';


        fetch(`../api/view_friend_schedule.php?friend_id=${encodeURIComponent(selectedFriendId)}&semester=${encodeURIComponent(semester)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    populateFriendSchedule(data.schedule);
                } else {
                    displayNotification(data.error || 'Failed to fetch friend\'s schedule.', 'error');
                    friendScheduleList.innerHTML = `<div class="caution caution-danger">${data.error || 'Failed to fetch schedule.'}</div>`;
                    friendScheduleList.innerHTML = `<div class="caution caution-danger">${data.error || 'Failed to fetch schedule.'}</div>`;
                }
            })
            .catch(err => {
                displayNotification('Error fetching friend\'s schedule. Please try again.', 'error');
                friendScheduleList.innerHTML = '<div class="caution caution-danger">An error occurred while fetching the schedule.</div>';
                friendScheduleList.innerHTML = '<div class="caution caution-danger">An error occurred while fetching the schedule.</div>';
            })
            .finally(() => {
                document.getElementById('scheduleLoading').style.display = 'none';
            });
    }
    function populateFriendSchedule(schedule) {
        const friendScheduleList = document.getElementById('friendScheduleList');
        friendScheduleList.innerHTML = ''; 
        friendScheduleList.innerHTML = ''; 

        if (schedule.length === 0) {
            friendScheduleList.innerHTML = '<div class="alert alert-info">No courses scheduled for this semester.</div>';
            return;
        }
        schedule.forEach(course => {
            const courseCard = document.createElement('div');
            courseCard.className = 'list-group-item list-group-item-action flex-column align-items-start mb-2';


            const courseHeader = document.createElement('div');
            courseHeader.className = 'd-flex w-100 justify-content-between';

            const courseTitle = document.createElement('h5');
            courseTitle.textContent = `${course.course_code} - ${course.course_name}`;

            const courseTime = document.createElement('small');
            courseTime.textContent = `${course.day_of_week} | ${course.start_time} - ${course.end_time}`;


            courseHeader.appendChild(courseTitle);
            courseHeader.appendChild(courseTime);


            const courseLocation = document.createElement('p');
            courseLocation.className = 'mb-1';
            courseLocation.textContent = `Location: ${course.location}`;



            courseCard.appendChild(courseHeader);
            courseCard.appendChild(courseLocation);
            friendScheduleList.appendChild(courseCard);
        });
    }

    sendFriendButton.addEventListener('click', sendFriendRequest);

    

    sendFriendInput.addEventListener('keyup', function (e) {
        if (e.key === 'Enter') {
            sendFriendRequest();
        }
    });

    friendSemesterSelect.addEventListener('change', getFriendSchedule);

    confirmActionButton.addEventListener('click', function () {
        if (!selectedAction || !selectedFriendData.id) {
            displayNotification('No action selected.', 'error');
            confirmationModal.hide();
            return;
        }

        if (selectedAction === 'removeFriend') {
            const friendId = selectedFriendData.id;

            fetch('../api/remove_friend.php', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ friend_id: friendId })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        displayNotification(data.message, 'success');
                        getFriendsAndRequests();
                    } else {
                        displayNotification(data.error || 'Failed to remove friend.', 'error');
                    }
                })
                .catch(err => {
                    displayNotification('Error removing friend. Please try again.', 'error');
                })
                .finally(() => {
                    selectedAction = null;
                    selectedFriendData = {};
                    confirmationModal.hide();
                });

        } else if (selectedAction === 'acceptFriendRequest') {
            const requesterId = selectedFriendData.id;

            fetch('../api/accept_friend_request.php', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ requester_id: requesterId })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        displayNotification(data.message, 'success');
                        getFriendsAndRequests();
                    } else {
                        displayNotification(data.error || 'Failed to accept friend request.', 'error');
                    }
                })
                .catch(err => {
                    displayNotification('Error accepting friend request. Please try again.', 'error');
                })
                .finally(() => {
                    selectedAction = null;
                    selectedFriendData = {};
                    confirmationModal.hide();
                });
        } else {
            displayNotification('Unknown action.', 'error');
            confirmationModal.hide();
        }
    });

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


    getFriendsAndRequests();
});

// coded by Jaiten Kang
document.addEventListener('DOMContentLoaded', function () {
    for (const [degree_id, degree] of Object.entries(degreeProgressData)) {
        const ctx = document.getElementById(`progressChart_${degree_id}`).getContext('2d');
        const completed = degree.completed_count;
        const total = degree.total_required;
        const progress = degree.progress;

        const data = {
            labels: ['Completed', 'Remaining'],
            datasets: [{
                data: [completed, total - completed],
                backgroundColor: ['#4caf50', '#e0e0e0'],
                borderWidth: 0
            }]
        };
        const options = {
            cutout: '70%',
            rotation: -90,
            circumference: 180,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: false
                },
                beforeDraw: function(chart) {
                    const width = chart.width,
                          height = chart.height,
                          ctx = chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em sans-serif";
                    ctx.textBaseline = "middle";

                    const text = `${progress}%`,
                          textX = Math.round((width - ctx.measureText(text).width) / 2),
                          textY = height / 1.5;
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                }
            }
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: options
        });
    }
});

function autocomplete(inp, fetchURL) {
    let currentFocus;

    inp.addEventListener("input", function(e) {
        let a, b, i, val = this.value;
        closeAllLists();
        if (!val) { return false;}
        currentFocus = -1;
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        this.parentNode.appendChild(a);
        fetch(fetchURL + "?query=" + encodeURIComponent(val))
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    const noMatchDiv = document.createElement("DIV");
                    noMatchDiv.innerHTML = "<strong>No matches found</strong>";
                    a.appendChild(noMatchDiv);
                    return;
                }

                data.forEach(course => {
                    let b = document.createElement("DIV");
                    const regex = new RegExp("(" + val + ")", "gi");
                    const courseCode = course.course_code.replace(regex, "<strong>$1</strong>");
                    const courseName = course.course_name.replace(regex, "<strong>$1</strong>");
                    b.innerHTML = courseCode + " - " + courseName;

                    const isCompleted = completedCourses.includes(course.course_code.toUpperCase());

                    if (isCompleted) {
                        b.innerHTML += ' <span class="badge bg-success ms-2">Completed</span>';
                        b.classList.add('completed');
                    }




                    b.innerHTML += "<input type='hidden' value='" + course.course_code + "'>";

                    if (!isCompleted) {
                        b.addEventListener("click", function(e) {
                            const courseCode = this.getElementsByTagName("input")[0].value;
                            inp.value = courseCode; 
                            let hiddenInput = document.getElementById('hidden_course_code');
                            if (!hiddenInput) {
                                hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'course_code';
                                hiddenInput.id = 'hidden_course_code';
                                inp.parentNode.appendChild(hiddenInput);
                            }
                            hiddenInput.value = courseCode;
                            closeAllLists();
                        });
                    }
                    a.appendChild(b);
                });
            })
            .catch(error => {
                console.error('Error fetching course data:', error);
            });
    });

    inp.addEventListener("keydown", function(e) {
        let x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
            currentFocus++;
            addActive(x);
        } else if (e.keyCode == 38) { 
            currentFocus--;
            addActive(x);
        } else if (e.keyCode == 13) {
            e.preventDefault();
            if (currentFocus > -1) {
                if (x) x[currentFocus].click();
            }
        }
    });


    function addActive(x) {
        if (!x) return false;
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        x[currentFocus].classList.add("autocomplete-active");
    }

    function removeActive(x) {
        for (let i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
        }
    }





    function closeAllLists(elmnt) {
        const x = document.getElementsByClassName("autocomplete-items");
        for (let i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }

    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    autocomplete(document.getElementById("course_code"), "../api/search_courses.php");
});

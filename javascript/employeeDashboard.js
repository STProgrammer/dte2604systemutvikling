"use strict";

function updateSelects(tasks) {
  return function (event) {
    const newValue = event && event.target && event.target.value;
    const categorySelect = document.getElementById(
      "employee_dashboard_category"
    );
    const locationSelect = document.getElementById(
      "employee_dashboard_location"
    );
    //console.log(categorySelect.value);
    if (
      newValue ? newValue === "Prosjekt" : categorySelect.value === "Prosjekt"
    ) {
      locationSelect.hidden = false;
      locationSelect.innerHTML = "";
      console.log(tasks);
      tasks.forEach((value) => {
        locationSelect.innerHTML += `<option value="${value.taskID}">${
          value.taskName ? value.taskName : "-"
        }</option>`;
      });
    } else if(tasks.length <= 0){
      locationSelect.hidden = false;
      locationSelect.innerHTML = "Empty";
    } else {
      locationSelect.hidden = true;
    }
  };
}

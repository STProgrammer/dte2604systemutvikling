"use strict";

const defaultArray = ["Hjemmekontor", "Kontor", "Ute", "Reise"];

function updateSelects(tasks) {
  return function (event) {
    const newValue = event && event.target && event.target.value;
    const categorySelect = document.getElementById(
      "employee_dashboard_category"
    );
    const locationSelect = document.getElementById(
      "employee_dashboard_location"
    );
    console.log(categorySelect.value);
    if (
      newValue ? newValue === "Prosjekt" : categorySelect.value === "Prosjekt"
    ) {
      locationSelect.innerHTML = "";
      tasks.forEach((value) => {
        locationSelect.innerHTML += `<option value="${value.groupName}">${
          value.groupName ? value.groupName : "-"
        }</option>`;
      });
    } else {
      locationSelect.innerHTML = "";
      defaultArray.forEach((value) => {
        locationSelect.innerHTML += `<option value="${value}">${value}</option>`;
      });
    }
  };
}

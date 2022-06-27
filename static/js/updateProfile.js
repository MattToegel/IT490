$(document).ready(function () {
    console.log("updateProfile.js loaded");
    $("#edit").click(function () {
        console.log("Edit Button clicked");
        $("#profile-confirm-form").slideToggle("slow", function () {
            console.log("SlideToggle complete");
        });
        $("#profile-form").slideToggle("slow", function () {
            console.log("SlideToggle complete");
        });
    });
    $("#cancel").click(function () {
        console.log("Cancel Button clicked");
        $("#profile-confirm-form").slideToggle("slow", function () {
            console.log("SlideToggle complete");
        });
        $("#profile-form").slideToggle("slow", function () {
            console.log("SlideToggle complete");
        });
    });
});
"use strict";

/* Admin console behaviour, adapted from quizapp's assets/js/app.js (no jQuery/toastr/SweetAlert). */
document.addEventListener("DOMContentLoaded", function () {
    const root = document.documentElement;

    // Desktop sidebar collapse (remembered per browser)
    const sidebarToggle = document.getElementById("sidebar-toggle");
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", function (e) {
            e.preventDefault();
            document.body.classList.toggle("sidebar-collapsed");
            try { localStorage.setItem("ot_sidebar_state", document.body.classList.contains("sidebar-collapsed") ? "collapsed" : "expanded"); } catch (err) {}
        });
    }

    // Mobile off-canvas sidebar
    const sidebar = document.querySelector(".sidebar");
    const overlay = document.querySelector(".sidebar-overlay");
    const openMobile = function (e) { e.preventDefault(); sidebar.classList.add("show"); if (overlay) overlay.classList.add("show"); };
    const closeMobile = function () { sidebar.classList.remove("show"); if (overlay) overlay.classList.remove("show"); };
    const mobileToggle = document.getElementById("mobile-toggle");
    const mobileClose = document.getElementById("mobile-close");
    if (mobileToggle && sidebar) mobileToggle.addEventListener("click", openMobile);
    if (mobileClose) mobileClose.addEventListener("click", closeMobile);
    if (overlay) overlay.addEventListener("click", closeMobile);
    document.addEventListener("keydown", function (e) { if (e.key === "Escape" && sidebar) closeMobile(); });

    // Sidebar groups
    document.querySelectorAll(".nav-dropdown-toggle").forEach(function (toggle) {
        toggle.addEventListener("click", function (e) {
            e.preventDefault();
            const expanded = this.getAttribute("aria-expanded") === "true";
            this.setAttribute("aria-expanded", String(!expanded));
            const menu = document.getElementById(this.getAttribute("aria-controls"));
            if (menu) menu.classList.toggle("show");
        });
        toggle.addEventListener("keydown", function (e) { if (e.key === "Enter" || e.key === " ") { e.preventDefault(); this.click(); } });
    });

    // Light / dark theme
    const themeToggle = document.getElementById("theme-toggle");
    if (themeToggle) {
        themeToggle.addEventListener("click", function () {
            const next = root.getAttribute("data-bs-theme") === "dark" ? "light" : "dark";
            root.setAttribute("data-bs-theme", next);
            try { localStorage.setItem("ot_theme", next); } catch (err) {}
        });
    }

    // Confirm before destructive or important actions: <button data-confirm="Are you sure?">
    document.querySelectorAll("[data-confirm]").forEach(function (el) {
        el.addEventListener("click", function (e) {
            if (!window.confirm(this.getAttribute("data-confirm") || "Are you sure?")) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        });
    });
});

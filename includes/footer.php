<?php
?>
<footer class="footer bg-dark text-white py-3 mt-5">
    <div class="container text-center">
        <p class="mb-0">© <?php echo date('Y'); ?> Advanced Schedule Builder. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php
    if (isset($pageJS)) {
        foreach ($pageJS as $jsFile) {
            echo '<script src="' . htmlspecialchars($jsFile) . '"></script>';
        }
    }
?>
</body>
</html>

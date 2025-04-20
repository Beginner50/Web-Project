<?php if (!empty($errors)): ?>
    <base href="/website/">
    <link rel="stylesheet" href="stylesheets/partials/errorModal.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <div id="error-modal" class="modal-overlay">
        <div class="modal-box roundBorder-10">
            <h2>There were some issues:</h2>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button id="close-modal" class="modal-close-button">Close</button>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#close-modal').on('click', function() {
                $('#error-modal').fadeOut();
                window.location.href = "/";
            });
        });
    </script>
<?php endif; ?>
<?php
$classes = [
    "2" => [
        ["ClassID" => 19, "Level" => 1, "ClassGroup" => "RED", "SubjectCode" => "MA102", "TeacherID" => null, "SubjectName" => "Calculus I"],
        ["ClassID" => 67, "Level" => 1, "ClassGroup" => "RED", "SubjectCode" => "MA304", "TeacherID" => null, "SubjectName" => "Discrete Mathematics"],
        ["ClassID" => 73, "Level" => 1, "ClassGroup" => "RED", "SubjectCode" => "CS305", "TeacherID" => null, "SubjectName" => "Computer Networks"],
        ["ClassID" => 79, "Level" => 1, "ClassGroup" => "RED", "SubjectCode" => "CS306", "TeacherID" => null, "SubjectName" => "Artificial Intelligence"]
    ]
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Education Portal Class Tab</title>
    <base href="/website/">
    <link rel="stylesheet" href="stylesheets/partials/sidebar.css">
    <link rel="stylesheet" href="stylesheets/common.css">
    <link rel="stylesheet" href="stylesheets/classMessaging/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>

<body>
    <?php require 'views/partials/navBar.php'; ?>

    <div class="flex-col">
        <aside class="sidebar">
            <h2>Classes</h2>
            <menu id="class-menu">
                <?php foreach ($classes["2"] as $class): ?>
                    <li class="class-entry"
                        data-id="<?= $class['ClassID'] ?>"
                        data-name="<?= htmlspecialchars($class['SubjectName']) ?>"
                        data-level="<?= $class['Level'] ?>"
                        data-group="<?= $class['ClassGroup'] ?>"
                        data-subject="<?= $class['SubjectCode'] ?>">
                        <?= htmlspecialchars($class['SubjectName']) ?>
                    </li>
                <?php endforeach; ?>
            </menu>
        </aside>

        <div id="main-wrapper">
            <!-- Chat Header -->
            <div id="classChat-header" class="classChat" style="display: none;">
                <div id="left-section">
                    <img src="assets/schedule-svgrepo-com.svg" alt="Schedule Icon">
                    <div id="classChat-description"></div>
                </div>
                <button id="viewMembers-button" class="indigoTheme roundBorder shadow">
                    View Members
                </button>
            </div>

            <!-- Chat Body -->
            <div id="classChat-body" class="classChat" style="display: none;"></div>

            <!-- Chat Footer -->
            <div id="classChat-footer" class="classChat" style="display: none;">
                <form id="message-form" method="post" action="ClassMessaging/sendMessage.php" style="display: flex; width: 100%; align-items: center;">
                    <textarea name="message-input" id="message-input" placeholder="Input your message here"></textarea>
                    <button id="send-button" type="submit">
                        <img src="assets/send.svg" alt="Send">
                    </button>
                </form>
            </div>


            <!-- Cover Message -->
            <div id="classChat-cover">No Class Selected</div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const classEntries = document.querySelectorAll('.class-entry');
            const header = document.getElementById('classChat-header');
            const body = document.getElementById('classChat-body');
            const footer = document.getElementById('classChat-footer');
            const cover = document.getElementById('classChat-cover');
            const description = document.getElementById('classChat-description');

            classEntries.forEach(entry => {
                entry.addEventListener('click', () => {
                    const subject = entry.dataset.subject;
                    const level = entry.dataset.level;
                    const group = entry.dataset.group;
                    const name = entry.dataset.name;

                    // Update header
                    description.textContent = `${name} (Level ${level} - Group ${group})`;

                    // Show interface
                    header.style.display = 'flex';
                    body.style.display = 'block';
                    footer.style.display = 'flex';
                    cover.style.display = 'none';

                    // Clear previous messages and simulate loading (you can fetch real messages here)
                    body.innerHTML = `<div class='message system'>Welcome to ${name} chat.</div>`;
                });
            });

            document.getElementById('message-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const msg = document.getElementById('message-input').value.trim();
                if (msg) {
                    const msgDiv = document.createElement('div');
                    msgDiv.className = 'message user';
                    msgDiv.textContent = msg;
                    body.appendChild(msgDiv);
                    this.reset();
                }
            });

            document.getElementById('viewMembers-button').addEventListener('click', function() {
                alert("Feature coming soon: Show class members.");
            });
        });
    </script>
</body>

</html>
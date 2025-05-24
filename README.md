<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Search and Lead Tracker</title>
</head>
<body>

  <h1>📊 Search and Lead Tracker</h1>
  <p><strong>Search and Lead Tracker</strong> is a PHP-based tool that allows users to find and track business leads across platforms like Instagram, TikTok, LinkedIn, and Facebook. It features status tracking and a lead database for organized outreach.</p>

  <h2>🚀 Features</h2>
  <ul>
    <li>Multi-platform lead search (Instagram, TikTok, LinkedIn, Facebook)</li>
    <li>Database storage of lead info</li>
    <li>Status tracking for each lead</li>
    <li>PHP-based, lightweight frontend</li>
  </ul>

  <h2>📁 Project Structure</h2>
  <pre>
Search-and-Lead-Tracker/
├── api/                --> API search scripts
├── assets/             --> CSS, images
├── config/             --> DB and API config
├── database/           --> SQL files for setup
├── index.php           --> Main dashboard
├── myleads.php         --> Lead tracker UI
└── LICENSE.md          --> License info
  </pre>

  <h2>⚙️ Installation</h2>
  <ol>
    <li>Clone the repo:
      <pre><code>git clone https://github.com/Vexx-bit/Search-and-Lead-Tracker.git</code></pre>
    </li>
    <li>Set up a MySQL database and import SQL files from <code>/database</code></li>
    <li>Configure DB/API settings in <code>/config</code></li>
    <li>Run locally on Apache with PHP enabled (e.g., XAMPP/WAMP)</li>
    <li>Visit: <code>http://localhost/Search-and-Lead-Tracker/index.php</code></li>
  </ol>

  <h2>🛠️ Usage</h2>
  <ul>
    <li>Search for leads via the dashboard</li>
    <li>Track captured leads via <code>myleads.php</code></li>
    <li>Update lead statuses (e.g., Contacted, Replied, Closed)</li>
  </ul>

  <h2>📄 License</h2>
  <p>This project is licensed under the terms of the <a href="https://github.com/Vexx-bit/Search-and-Lead-Tracker/blob/main/LICENSE.md">LICENSE.md</a>.</p>

  <p>Developed with ❤️ by <a href="https://github.com/Vexx-bit">Vexx-bit</a></p>

</body>
</html>

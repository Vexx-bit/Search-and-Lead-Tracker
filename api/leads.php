<?php
header('Content-Type: application/json');
require_once '../config/database.php';

// Handle CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Function to check if lead exists
function checkLeadExists($conn, $company_name) {
    $sql = "SELECT id, status FROM leads WHERE company_name = '" . $conn->real_escape_string($company_name) . "'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Fetch leads from database
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['source']) && $_GET['source'] === 'database') {
    $niche = isset($_GET['niche']) ? $_GET['niche'] : '';
    $location = isset($_GET['location']) ? $_GET['location'] : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 10; // Number of items per page
    $offset = ($page - 1) * $perPage;
    
    $sql = "SELECT * FROM leads WHERE 1=1";
    if ($niche) {
        $sql .= " AND niche LIKE '%" . $conn->real_escape_string($niche) . "%'";
    }
    if ($location) {
        $sql .= " AND location LIKE '%" . $conn->real_escape_string($location) . "%'";
    }
    
    // Get total count for pagination
    $countResult = $conn->query($sql);
    $totalItems = $countResult->num_rows;
    $totalPages = ceil($totalItems / $perPage);
    
    // Add pagination to query
    $sql .= " LIMIT $offset, $perPage";
    
    $result = $conn->query($sql);
    $leads = [];
    while ($row = $result->fetch_assoc()) {
        $leads[] = $row;
    }
    
    echo json_encode([
        'leads' => $leads,
        'pagination' => [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
            'perPage' => $perPage
        ]
    ]);
}

// Fetch leads from Google Custom Search API
else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['source']) && $_GET['source'] === 'google') {
    $apiKey = 'AIzaSyC0sq5-_lJgbgvK8g4ZdZd38vYxlzU6Ais';
    $cx = '05327eefe87954be5';
    $query = isset($_GET['query']) ? urlencode($_GET['query']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = (($page - 1) * 10) + 1; // Google uses 1-based index
    
    if ($query) {
        $url = "https://www.googleapis.com/customsearch/v1?q=$query&key=$apiKey&cx=$cx&start=$start";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($httpCode === 200) {
            $searchResults = json_decode($response, true);
            if (isset($searchResults['items'])) {
                // Check database status for each result
                foreach ($searchResults['items'] as &$item) {
                    $existingLead = checkLeadExists($conn, $item['title']);
                    $item['inDatabase'] = !empty($existingLead);
                    $item['leadStatus'] = $existingLead ? $existingLead['status'] : 'new';
                    $item['leadId'] = $existingLead ? $existingLead['id'] : null;
                    
                    // Get image from pagemap if available
                    if (isset($item['pagemap']['cse_image'][0]['src'])) {
                        $item['image_url'] = $item['pagemap']['cse_image'][0]['src'];
                    } elseif (isset($item['pagemap']['imageobject'][0]['url'])) {
                        $item['image_url'] = $item['pagemap']['imageobject'][0]['url'];
                    } else {
                        $item['image_url'] = null;
                    }
                }
            }
            
            // Add pagination information
            $searchResults['pagination'] = [
                'currentPage' => $page,
                'totalPages' => ceil(($searchResults['searchInformation']['totalResults'] ?? 0) / 10),
                'totalItems' => (int)($searchResults['searchInformation']['totalResults'] ?? 0),
                'perPage' => 10
            ];
            
            echo json_encode($searchResults);
        } else {
            http_response_code($httpCode);
            echo json_encode(['error' => 'Failed to fetch data from Google API']);
        }
        
        curl_close($ch);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Query parameter is required']);
    }
}

// POST request - Create new lead
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (empty($data['company_name']) || empty($data['niche']) || empty($data['location'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit();
    }
    
    // Check if lead already exists
    $existingLead = checkLeadExists($conn, $data['company_name']);
    if ($existingLead) {
        http_response_code(409);
        echo json_encode(['error' => 'Lead already exists', 'lead' => $existingLead]);
        exit();
    }
    
    // Insert new lead
    $sql = "INSERT INTO leads (company_name, niche, location, contact_person, email, phone, notes, platform, url, status) 
            VALUES (
                '" . $conn->real_escape_string($data['company_name']) . "',
                '" . $conn->real_escape_string($data['niche']) . "',
                '" . $conn->real_escape_string($data['location']) . "',
                '" . $conn->real_escape_string($data['contact_person'] ?? '') . "',
                '" . $conn->real_escape_string($data['email'] ?? '') . "',
                '" . $conn->real_escape_string($data['phone'] ?? '') . "',
                '" . $conn->real_escape_string($data['notes'] ?? '') . "',
                '" . $conn->real_escape_string($data['platform'] ?? '') . "',
                '" . $conn->real_escape_string($data['url'] ?? '') . "',
                'new'
            )";
    
    if ($conn->query($sql)) {
        $newId = $conn->insert_id;
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Lead created successfully',
            'leadId' => $newId,
            'status' => 'new'
        ]);
    } else {
        error_log("MySQL Error: " . $conn->error . "\nQuery: " . $sql);
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to add lead: ' . $conn->error
        ]);
    }
}

// Handle PUT requests (update lead)
else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing lead ID']);
        exit();
    }
    
    // Update lead status
    if (isset($data['status']) && count($data) === 1) {
        $sql = "UPDATE leads SET 
                status = '" . $conn->real_escape_string($data['status']) . "'
                WHERE id = $id";
    } else {
        // Full lead update
        $sql = "UPDATE leads SET 
            company_name = '" . $conn->real_escape_string($data['company_name']) . "',
            niche = '" . $conn->real_escape_string($data['niche']) . "',
            location = '" . $conn->real_escape_string($data['location']) . "',
            contact_person = '" . $conn->real_escape_string($data['contact_person'] ?? '') . "',
            email = '" . $conn->real_escape_string($data['email'] ?? '') . "',
            phone = '" . $conn->real_escape_string($data['phone'] ?? '') . "',
            status = '" . $conn->real_escape_string($data['status']) . "',
            notes = '" . $conn->real_escape_string($data['notes'] ?? '') . "',
            platform = '" . $conn->real_escape_string($data['platform'] ?? '') . "',
            url = '" . $conn->real_escape_string($data['url'] ?? '') . "'
            WHERE id = $id";
    }
                
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Lead updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update lead']);
    }
    exit();
}

// Handle DELETE requests
else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing lead ID']);
        exit();
    }
    
    $sql = "DELETE FROM leads WHERE id = $id";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Lead deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete lead']);
    }
    exit();
}

$conn->close();

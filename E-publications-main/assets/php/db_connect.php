<?php
include 'conn.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $author_name = isset($_POST['author_name']) ? trim($_POST['author_name']) : '';
    $author_pseudonym = isset($_POST['author_pseudonym']) ? trim($_POST['author_pseudonym']) : '';
    $editor_name = isset($_POST['editor_name']) ? trim($_POST['editor_name']) : '';
    $title_of_publication = isset($_POST['title_of_publication']) ? trim($_POST['title_of_publication']) : '';
    $book_edition = isset($_POST['book_edition']) ? trim($_POST['book_edition']) : '';
    $impression = isset($_POST['impression']) ? trim($_POST['impression']) : '';
    $isbn_electronic = isset($_POST['isbn_electronic']) ? trim($_POST['isbn_electronic']) : '';
    $set_isbn = isset($_POST['set_isbn']) ? trim($_POST['set_isbn']) : '';
    $publisher_name = isset($_POST['publisher_name']) ? trim($_POST['publisher_name']) : '';
    $publisher_address = isset($_POST['publisher_address']) ? trim($_POST['publisher_address']) : '';
    $publisher_year = isset($_POST['publication_year']) ? trim($_POST['publication_year']) : '';
    $price = isset($_POST['price']) ? trim($_POST['price']) : '';
    $fiction_or_non = isset($_POST['fiction_or_nonfiction']) ? trim($_POST['fiction_or_nonfiction']) : '';
    $genre = isset($_POST['genre']) ? trim($_POST['genre']) : '';
    $langauge_of_publication = isset($_POST['language_of_publication']) ? trim($_POST['language_of_publication']) : '';
    $english_translation = isset($_POST['english_translation_title']) ? trim($_POST['english_translation_title']) : '';
    $file = isset($_POST['file']) ? trim($_POST['file']) : '';

    if (empty($author_name) || empty($email) || empty($author_name)|| empty($author_pseudonym) || empty($editor_name)|| empty($title_of_publication)|| empty($book_edition)||empty($impression) ||empty($isbn_electronic)||empty($set_isbn)||empty($publisher_name)||empty($publisher_address)||empty($publisher_year)||empty($price)||empty($fiction_or_non)||empty($genre)||empty($langauge_of_publication)||empty($english_translation)||empty($file)) {
        echo "Please ensure that all fields are filled.";
        exit;
    }

    if (isset($_FILES["file"]) && $_FILES["file"]["error"] === UPLOAD_ERR_OK) {
        
        // Seting absolute path for the uploads directory
        $targetDir = __DIR__ . "/../../uploads/";
        
        // Check if directory exists, if not, create it
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // Ensure it's writable
        }

        $fileName = basename($_FILES["file"]["name"]);
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedTypes = array('jpg', 'png', 'jpeg', 'gif', 'pdf');

        if (in_array($fileType, $allowedTypes)) {
            $newFileName = uniqid() . "_" . $fileName;
            $targetFilePath = $targetDir . $newFileName;

            // Attempt to move the uploaded file
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
                if ($conn && $conn->ping()) {
                    $stmt = $conn->prepare("INSERT INTO book_info(EmailAddress, AuthorName, EditorName, AuthorPusedonym, PublicationTitle, BookEdition, Impression, Isbn, SetISBN, PublisherName, PublisherAddress, PublicationYear, Price, FictionOrNonFiction, Genre, PublicationLanguage, EnglishVersionTitle, FileUpload) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssssssssssssss", $email, $author_name, $author_pseudonym, $editor_name, $title_of_publication, $book_edition, $impression, $isbn_electronic, $set_isbn, $publisher_name, $publisher_address, $publisher_year, $price, $fiction_or_non, $genre, $langauge_of_publication, $english_translation, $newFileName);

                    if ($stmt->execute()) {
                        echo "File uploaded and data saved successfully.";
                    } else {
                        echo "Database error: " . $conn->error;
                    }

                    $stmt->close();
                } else {
                    echo "Database connection error.";
                }
            } else {
                echo "File upload error: Unable to move file.";
            }
        } else {
            echo "Invalid file type.";
        }
    } else {
        echo "No file uploaded or an upload error occurred.";
    }
}

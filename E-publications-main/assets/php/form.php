<?php
// Include database connection
require_once 'conn.php'; // Update the path to your connection file

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $authorName = htmlspecialchars($_POST['author_name']);
    $authorPseudonym = htmlspecialchars($_POST['author_pseudonym']);
    $editorName = htmlspecialchars($_POST['editor_name']);
    $publicationTitle = htmlspecialchars($_POST['title_of_publication']);
    $bookEdition = htmlspecialchars($_POST['book_edition']);
    $impression = htmlspecialchars($_POST['impression']);
    $isbn = intval($_POST['isbn_electronic']);
    $setISBN = htmlspecialchars($_POST['set_isbn']);
    $publisherName = htmlspecialchars($_POST['publisher_name']);
    $publisherAddress = htmlspecialchars($_POST['publisher_address']);
    $publicationYear = htmlspecialchars($_POST['publication_year']);
    $price = floatval($_POST['price']);
    $fictionOrNonFiction = htmlspecialchars($_POST['fiction_or_nonfiction']);
    $genre = htmlspecialchars($_POST['genre']);
    $publicationLanguage = htmlspecialchars($_POST['language_of_publication']);
    $englishVersionTitle = htmlspecialchars($_POST['english_translation_title']);

    // Handle file upload
    $uploadDir = 'uploads/';
    $fileName = basename($_FILES['file']['name']);
    $uploadFile = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        die("File upload failed.");
    }

    // Insert data into the database
    try {
        $query = "INSERT INTO book_informationsheet (
            PublisherEmail, AuthorName, AuthorPseudonym, EditorName, PublicationTitle, BookEdition, Impression, Isbn, SetISBN, PublisherName, PublisherAddress, PublicationYear, Price, FictionOrNonFiction, Genre, PublicationLanguage, EnglishVersionTitle, FileUpload
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            'ssssssssssssdssss',
            $email, $authorName, $authorPseudonym, $editorName, $publicationTitle, $bookEdition, $impression, $isbn, $setISBN, $publisherName, $publisherAddress, $publicationYear, $price, $fictionOrNonFiction, $genre, $publicationLanguage, $englishVersionTitle, $fileName
        );

        if ($stmt->execute()) {
            echo "Data inserted successfully!";
        } else {
            throw new Exception("Database insertion failed: " . $stmt->error);
        }
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

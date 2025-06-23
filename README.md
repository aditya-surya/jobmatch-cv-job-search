
Built by https://www.blackbox.ai

---

# JobMatch - Pencari Lowongan Kerja

## Project Overview

**JobMatch** is a web application designed to help job seekers find their ideal jobs by matching their CVs against job openings available on various platforms like LinkedIn and Jobstreet. The application utilizes a Naive Bayes algorithm to analyze the qualifications in the user's CV and present the most suitable job matches.

## Installation

To set up the JobMatch application, follow these steps:

1. **Clone the repository:**
   ```bash
   git clone https://github.com/username/jobmatch.git
   cd jobmatch
   ```

2. **Install Dependencies:**
   Ensure you have PHP and Composer installed. In the project directory, install the required PHP packages using Composer:
   ```bash
   composer install
   ```

3. **Setup Database Connection:**
   Create a file named `config/database.php` to set up the database connection. Ensure to define the connection parameters as required by your environment.

4. **Create Database Tables:**
   Create the necessary tables in your database as required by the application.

5. **Start the local server:**
   If you are using XAMPP, WAMP, or MAMP, place the project in the `htdocs` (or equivalent) directory and start the server. Alternatively, you can use the built-in PHP server:
   ```bash
   php -S localhost:8000
   ```

## Usage

1. Open your web browser and navigate to `http://localhost/jobmatch/index.html`.
2. Click on "Mulai Sekarang" to proceed to the upload page.
3. Upload your CV in PDF, DOC, or DOCX format.
4. The application processes your CV and matches it with relevant job listings.
5. View the results and details of the job matches.

## Features

- **CV Upload:** Users can upload CVs in supported file formats (PDF, DOC, DOCX).
- **Job Matching:** Automatically matches user CVs with available job postings using Naive Bayes algorithm.
- **Job Details:** Users can view detailed descriptions of job postings, including requirements and other relevant information.
- **Progress Display:** Users receive feedback on the upload and processing status, including error messages if necessary.

## Dependencies

The project relies on the following PHP packages, as defined in `composer.json`:

- `smalot/pdfparser`: For PDF parsing.
- `phpoffice/phpword`: For parsing Word documents (DOC, DOCX).

You can install these dependencies using Composer during the installation step.

## Project Structure

Here’s a brief overview of the project structure:

```
jobmatch/
│
├── index.html                  # Landing page
├── upload.php                  # Page for uploading CV
├── process_cv.php              # Handles CV processing and matching jobs
├── results.php                 # Page to show matching job results
├── job_detail.php              # Shows detailed information for a specific job
│
├── config/
│   └── database.php            # Database connection setup
│
├── classes/
│   ├── NaiveBayes.php          # Class for Naive Bayes algorithm implementation
│   └── CVParser.php            # Class for parsing CVs from uploaded files
│
└── css/
    └── style.css               # Custom styles for application UI
```

## Contributing

Contributions are welcome! If you have suggestions or improvements, feel free to open a pull request or raise an issue.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
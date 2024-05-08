
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('generatePdfButton').addEventListener('click', function () {
            // Create a new jsPDF instance
            const doc = new window.jsPDF();

            // Add HTML content to the PDF
            var element = document.getElementById('pdfContent'); // 'pdfContent' is the ID of the element containing your HTML content
            doc.html(element, {
                callback: function () {
                    // Save the PDF or open it in a new tab
                    doc.save('document.pdf'); // or doc.output('dataurlnewwindow');
                }
            });
        });
    });

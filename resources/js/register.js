// resources/js/register.js

document.addEventListener('DOMContentLoaded', function() {
    const schoolSelect = document.getElementById('school_id');
    const classSelect = document.getElementById('class_id');

    if (schoolSelect && classSelect) {
        schoolSelect.addEventListener('change', function() {
            const schoolId = this.value;

            // Clear existing options
            classSelect.innerHTML = '<option value="">Memuat kelas...</option>';
            classSelect.disabled = true;

            if (schoolId) {
                // Fetch classes by school
                fetch('/get-classes-by-school', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ school_id: schoolId })
                })
                .then(response => response.json())
                .then(data => {
                    classSelect.innerHTML = '<option value="">Pilih Kelas</option>';

                    if (data.length > 0) {
                        data.forEach(classItem => {
                            const option = document.createElement('option');
                            option.value = classItem.id;
                            option.textContent = classItem.name;
                            classSelect.appendChild(option);
                        });
                        classSelect.disabled = false;
                    } else {
                        classSelect.innerHTML = '<option value="">Tidak ada kelas tersedia</option>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    classSelect.innerHTML = '<option value="">Error memuat kelas</option>';
                });
            } else {
                classSelect.innerHTML = '<option value="">Pilih sekolah terlebih dahulu</option>';
            }
        });
    }
});

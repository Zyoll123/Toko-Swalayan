document.addEventListener('DOMContentLoaded', function () {
    function saveInputValues() {
        const inputs = document.querySelectorAll('.number-input')
        const savedValues = {}

        inputs.forEach(input => {
            const productId = input.name.match(/\[(\d+)\]/)[1]
            savedValues[productId] = input.value
        })

        sessionStorage.setItem('savedInputValues', JSON.stringify(savedValues))
    }

    function restoreInputValues() {
        const savedValues = JSON.parse(sessionStorage.getItem('savedInputValues') || '{}')

        document.querySelectorAll('.number-input').forEach(input => {
            const productId = input.name.match(/\[(\d+)\]/)[1]
            if (savedValues[productId] && savedValues[productId] > 0) {
                input.value = savedValues[productId]
            }
        })

        updateTotalHarga()
    }

    const searchInput = document.getElementById('liveSearch')
    let searchTimer

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer)

        searchTimer = setTimeout(() => {
            const searchValue = this.value.trim()
            updateSearchresult(searchValue)
        }, 500);
    })

    function updateSearchresult(searchTerm) {
        const url = new URL(window.location.href)

        if (searchTerm) {
            url.searchParams.set('search', searchTerm)
        } else {
            url.searchParams.delete('search')
        }

        const scrollPosition = window.scrollY || window.pageYOffset
        window.location.href = url.toString()

        window.onload = function () {
            window.scrollTo(0, scrollPosition)
            restoreInputValues()
        }
    }

    function setupQuantityButtons() {
        document.querySelectorAll('.plusBtn').forEach((button) => {
            button.addEventListener('click', (e) => {
                const input = e.target.parentElement.querySelector('.number-input')
                if (input.value === '' || isNaN(input.value)) {
                    input.value = 0
                }
                input.value = parseInt(input.value) + 1
                updateTotalHarga()
            })
        })
    
        document.querySelectorAll('.minusBtn').forEach((button) => {
            button.addEventListener('click', (e) => {
                const input = e.target.parentElement.querySelector('.number-input')
                const max = parseInt(input.getAttribute('max'))
                let value = parseInt(input.value)
                if (input.value === '' || isNaN(input.value)) {
                    input.value = 0
                }
                if (input.value > 0) {
                    input.value = parseInt(input.value) - 1
                    updateTotalHarga()
                }
            })
        })
    }
    
    function updateTotalHarga() {
        let total = 0
    
        document.querySelectorAll('.number-input').forEach((input) => {
            const quantity = parseInt(input.value) || 0
            const price = parseInt(input.getAttribute('price-data')) || 0
            total += quantity * price
        })
    
        const totalElement = document.getElementById('total-price')
        if (totalElement) {
            totalElement.textContent = total.toLocaleString()
        }
        // document.getElementById('total-price').textContent = total.toLocaleString()
    }

    setupQuantityButtons()
    restoreInputValues()
    updateTotalHarga()
    validateQuantity()
})

function validateQuantity(input) {
    const maxStock = parseInt(input.getAttribute('max'))
    let value = parseint(input.value)

    if (isNaN(value) || value < 1) {
        input.value = 1
    } else if (value > maxStock) {
        input.value = maxStock;
    }
}

function updateClock() {
    const now = new Date()

    const hours = String(now.getHours()).padStart(2, '0')
    const minutes = String(now.getMinutes()).padStart(2, '0')
    const seconds = String(now.getSeconds()).padStart(2, '0')
    const timeString = `${hours}:${minutes}:${seconds}`

    const day = String(now.getDate()).padStart(2, '0')
    const month = String(now.getMonth() + 1).padStart(2, '0')
    const year = now.getFullYear()
    const dateString = `${day}/${month}/${year}`

    const fullDateTime = `${dateString} ${timeString}`

    document.getElementById('liveClock').textContent = fullDateTime

    setTimeout(updateClock, 1000)
}

document.addEventListener('DOMContentLoaded', updateClock)
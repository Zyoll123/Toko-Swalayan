document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded'); 
    

    function setupRowClickHandlers() {
        document.querySelectorAll('.clickable-row').forEach(row => {
            row.addEventListener('click', function () {
                const id = this.getAttribute('data-id')
                const name = this.getAttribute('data-name')
                const stock = this.getAttribute('data-stock')
                const quantity = document.getElementById('Quantity')

                document.getElementById('IdSearch').value = id;
                document.getElementById('liveSearch').value = name;

                quantity.max = stock
                quantity.setAttribute('max', stock)

                if (parseInt(quantity.value) > stock) {
                    quantity.value = stock
                }

                document.getElementById('Quantity').focus()
            })
        })
    }

    document.querySelector('form').addEventListener('submit', function(e) {
        const quantityInput = document.getElementById('Quantity')
        const maxStock = parseInt(quantityInput.max) || 0
        const quantity = parseInt(quantityInput.value) || 0

        if (quantity > maxStock) {
            e.preventDefault()
            alert(`Stock tidak mencukupi! Stock tersedia: ${maxStock}`)
            quantityInput.value = maxStock
            quantityInput.focus()
        }
    })

    document.getElementById('liveSearch').addEventListener('input', function () {
        document.getElementById('IdSearch').value = ''
    })

    document.getElementById('IdSearch').addEventListener('input', function () {
        document.getElementById('liveSearch').value = ''
    })

    setupRowClickHandlers()
})

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
document.addEventListener("DOMContentLoaded", () => {
  // Inicializar todos os caroussels
  const caroussels = document.querySelectorAll(".caroussel")

  caroussels.forEach((caroussel) => {
    const images = caroussel.querySelectorAll("img")
    if (images.length > 0) {
      images[0].classList.add("active")
    }
  })

  const prevButtons = document.querySelectorAll(".prev")
  const nextButtons = document.querySelectorAll(".next")

  prevButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const carousselId = this.getAttribute("data-caroussel")
      changeSlide(carousselId, -1)
    })
  })

  nextButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const carousselId = this.getAttribute("data-caroussel")
      changeSlide(carousselId, 1)
    })
  })
})

function changeSlide(carousselId, direction) {
  const caroussel = document.querySelector(`.caroussel[data-caroussel="${carousselId}"]`)
  const images = caroussel.querySelectorAll("img")
  let currentIndex = 0

  // Encontrar a imagem ativa atual
  images.forEach((img, index) => {
    if (img.classList.contains("active")) {
      currentIndex = index
    }
  })

  // Remover classe active da imagem atual
  images[currentIndex].classList.remove("active")

  // Calcular novo índice
  let newIndex = currentIndex + direction

  // Loop circular
  if (newIndex < 0) {
    newIndex = images.length - 1
  } else if (newIndex >= images.length) {
    newIndex = 0
  }

  // Adicionar classe active à nova imagem
  images[newIndex].classList.add("active")
}
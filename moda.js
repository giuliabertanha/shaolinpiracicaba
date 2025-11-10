document.addEventListener("DOMContentLoaded", () => {
  // Inicializar todos os carousels
  const carousels = document.querySelectorAll(".caroussel")

  carousels.forEach((carousel) => {
    const images = carousel.querySelectorAll("img")
    if (images.length > 0) {
      images[0].classList.add("active")
    }
  })

  const prevButtons = document.querySelectorAll(".prev")
  const nextButtons = document.querySelectorAll(".next")

  prevButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const carouselId = this.getAttribute("data-carousel")
      changeSlide(carouselId, -1)
    })
  })

  nextButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const carouselId = this.getAttribute("data-carousel")
      changeSlide(carouselId, 1)
    })
  })
})

function changeSlide(carouselId, direction) {
  const carousel = document.querySelector(`.caroussel[data-carousel="${carouselId}"]`)
  const images = carousel.querySelectorAll("img")
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
    </main>
</div> 
<style>
    .custom-footer {
        background: linear-gradient(to right, #141e30, #243b55);
        border-top: 5px solid #00c6ff;
    }
    .footer-title {
        font-weight: 800;
        background: linear-gradient(45deg, #00c6ff, #0072ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1.5rem;
    }
    .footer-link {
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .footer-link:hover {
        color: #00c6ff;
    }
    .footer-icon {
        width: 30px;
        text-align: center;
        margin-right: 10px;
        color: #00c6ff;
    }
    .map-wrapper {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        border: 2px solid rgba(255,255,255,0.1);
    }
</style>

<footer class="custom-footer text-white pt-5 pb-3 mt-5">
    <div class="container">
        <div class="row g-4 mb-4">
            <!-- Left Column: Info -->
            <div class="col-lg-7">
                <h3 class="footer-title">Hệ Thống Đặt Sân Thể Thao</h3>
                <p class="mb-4 opacity-75" style="max-width: 500px;">
                    Nền tảng đặt sân thể thao hàng đầu, cung cấp trải nghiệm tốt nhất cho người yêu thể thao. Đặt sân dễ dàng, quản lý chuyên nghiệp.
                </p>

                <h5 class="fw-bold mb-3 text-white">Liên hệ với chúng tôi</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex align-items-center">
                        <i class="fas fa-map-marker-alt footer-icon fs-5"></i>
                        <span>180 Cao Lỗ, Phường 4, Quận 8, TP.HCM</span>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <i class="fas fa-envelope footer-icon fs-5"></i>
                        <a href="mailto:vdpvinh24@gmail.com" class="footer-link">vdpvinh24@gmail.com</a>
                    </li>
                    <li class="mb-3 d-flex align-items-center">
                        <i class="fas fa-phone-alt footer-icon fs-5"></i>
                        <a href="tel:0877399514" class="footer-link fw-bold text-warning">0877.399.514</a>
                    </li>
                </ul>
            </div>

            <!-- Right Column: Map -->
            <div class="col-lg-5">
        
                <div class="map-wrapper" style="height: 250px;">
                    <iframe 
                        src="https://www.google.com/maps?q=180+Cao+Lỗ,+Quận+8&output=embed" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>

        <div class="text-center border-top border-secondary pt-4 mt-4">
            <p class="text-muted mb-0">&copy; <span id="copyright-year"></span> SVD Pro - Soccer Field Management. All rights reserved.</p>
        </div>
    </div>
</footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="<?php echo BASE_URL; ?>public/js/main.js"></script>
</body>
</html>
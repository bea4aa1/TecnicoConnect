<footer class="footer-site">
    <div class="section grid-info" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px;">
        <div>
            <h3>Técnico Connect</h3>
            <p>O TCC que virou solução industrial.</p>
        </div>
        <div>
            <h4>Links</h4>
            <a href="quiz.php">Quiz</a><br>
            <a href="guia-curriculo.php">Currículo</a>
        </div>
        <div>
            <h4>Newsletter</h4>
            <form action="processar-newsletter.php" method="POST">
                <input type="email" name="email" placeholder="Seu e-mail técnico" class="newsletter-input" required>
                <button type="submit" class="btn-roxo newsletter-btn">Assinar</button>
            </form>
        </div>
    </div>
    <div style="text-align: center; margin-top: 40px; opacity: 0.6; font-size: 0.8rem;">
        &copy; <?php echo date('Y'); ?> Técnico Connect. Todos os direitos reservados.
    </div>
</footer>

<script src="script.js"></script>
</body>
</html>
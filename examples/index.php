<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>php-image examples</title>

  <style>
    :root {
      font-size: 15px;
    }

    html {
      box-sizing: border-box;
    }

    *,
    *::before,
    *::after {
      box-sizing: inherit;
      padding: 0;
      margin: 0;
    }

    img {
      max-width: 100%;
      margin: 0 auto;
    }

    body {
      max-width: 1200px;
      margin: 5rem auto;
    }

    h1 {
      margin-bottom: 2rem;
    }

    article {
      display: grid;
      grid-template-columns: 1fr 1fr;
      grid-gap: 1rem;
    }

    section {
      display: flex;
      flex-direction: column;
      justify-content: flex-end;

      text-align: center;
      border: 1px solid #ddd;
    }
  </style>
</head>

<body>
  <h1>Examples</h1>

  <article>
    <section>
      <h2>Image A</h2>
      <img src="a.png">
    </section>

    <section>
      <h2>Image B</h2>
      <img src="b.png">
    </section>

    <section>
      <h2>Merge</h2>
      <img src="merge.php" />
    </section>

    <section>
      <h2>Draw changes indicator without denoising</h2>
      <img src="changes-indicator-without-denoise.php" />
    </section>

    <section>
      <h2>Draw changes indicator denoising the standard deviation</h2>
      <img src="changes-indicator-denoise-standard-deviation.php" />
    </section>

    <section>
      <h2>Draw changes indicator with custom denoise value</h2>
      <img src="changes-indicator-custom-denoise.php" />
    </section>

    <section>
      <h2>Draw changing area</h2>
      <img src="draw-changing-area.php" />
    </section>
  </article>
</body>

</html>
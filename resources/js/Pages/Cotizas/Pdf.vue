<template>
    <div class="container">
        <div class="logo">
            <!-- Aquí puedes colocar el logo de tu empresa -->
            <img src="/ruta/a/la/imagen/logo.png" alt="Logo">
        </div>
        <div class="info">
            <h2>Cotización</h2>
            <table>
            <thead>
                <tr>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Capacidades</th>
                <th>Foto</th>
                <th>Especificaciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Itera sobre los productos agregados y muestra la información -->
                <tr v-for="(tbproducto, index) in tbproductosAgregados" :key="index">
                <td>{{ tbproducto.tbmarca ? tbproducto.tbmarca.nombre : 'Sin marca' }}</td>
                <td>{{ tbproducto.modelo }}</td>
                <td>{{ tbproducto.capacidades }}</td>
                <td><img :src="'/img/catalogo/' + tbproducto.foto" alt="Foto" style="max-width: 100px;"></td>
                <td>{{ tbproducto.especificaciones }}</td>
                </tr>
            </tbody>
            </table>
        </div>
        <div class="total">
            <p>Total: {{ form.total }} {{ form.igvEnabled ? ' (incluye IGV)' : ' (no incluye IGV)' }}</p>
        </div>
        <div class="footer">
            <!-- Aquí puedes colocar cualquier información adicional o un mensaje de despedida -->
            <p>Gracias por su preferencia.</p>
        </div>
    </div>
</template>
  
<script>
  export default {
    props: {
      tbproductosAgregados: {
        type: Array,
        required: true
      },
      form: {
        type: Object,
        required: true
      }
    }
  }
</script>
  
<style scoped>
  .container {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }
  .logo {
    text-align: center;
    margin-bottom: 20px;
  }
  .logo img {
    max-width: 200px;
  }
  .info {
    margin-bottom: 20px;
  }
  .info h2 {
    text-align: center;
  }
  .info table {
    width: 100%;
    border-collapse: collapse;
  }
  .info table th, .info table td {
    border: 1px solid #ccc;
    padding: 8px;
  }
  .total {
    text-align: right;
    margin-top: 20px;
  }
  .footer {
    text-align: center;
    margin-top: 20px;
    font-size: 12px;
    color: #666;
  }
</style>
  
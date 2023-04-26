$(document).ready(() => {
	
	$('#documentacao').on('click', () => {
		//$('#pagina').load('documentacao.html')

		/*
		$.get('documentacao.html', data => { 
			$('#pagina').html(data)
		})
		*/
		$.post('documentacao.html', data => { 
			$('#pagina').html(data)
		})
	})

	$('#suporte').on('click', () => {
		//$('#pagina').load('suporte.html')

		/*
		$.get('suporte.html', data => { 
			$('#pagina').html(data)
		})
		*/
		$.post('suporte.html', data => { 
			$('#pagina').html(data)
		})
	})

	/*implementando o método ajax para o request
	na mudança da opção desse select nós vamos disparar um evento 
	parametro (e) dispara e retorna um objeto/evento com diversos métodos e atributos 
	vamos acessar o método target do evento, recuperando o atributo 'value' deste método */
	$('#competencia').on('change', (e) => {

		let competencia = $(e.target).val()//atribui o valor(<option>) selecionado pelo usuário, à variável "competencia".

		/* precisamos definir nesse objeto literal: O Método da requisição, a url (para onde essa requisição será feita), 
		se haverá dados, e o que vamos fazer caso houver sucesso ou erro nesse processo */
		$.ajax({
			type: 'GET',
			url: 'app.php',
			data: `competencia=${competencia}`, // dados que serão enviados na requisição, (name=value)
			dataType: 'json', //parametrizamos a nossa requisição informando que o dado de resposta é JSON
			success: (dados) => {
				/* selecionando o elemento com o respectivo ID e,
				   através do método HTML vou atribuir/substituir 
				   como conteudo interno desse elemento('#numeroVendas') o valor que estamos recuperando como
				   resposta da nossa requisição (dados.numeroVendas) */
				$('#numeroVendas').html(dados.numeroVendas)
				$('#totalVendas').html(dados.totalVendas)

				$('#clientesAtivos').html(dados.clientesAtivos)
				$('#clientesInativos').html(dados.clientesInativos)
 
				$('#totalReclamacoes').html(dados.totalReclamacoes)
				$('#totalElogios').html(dados.totalElogios)
				$('#totalSugestoes').html(dados.totalSugestoes)
		
				$('#totalDespesas').html(dados.totalDespesas)
			},
			error: (erro) => { console.log(erro) }
		})
	})
})
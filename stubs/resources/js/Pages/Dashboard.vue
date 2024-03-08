<script setup>
import Layout from '@/Layouts/Admin/Layout.vue'
// import Layout from '@/Layouts/AuthenticatedLayout.vue';
// import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/Components/ui/card'
import { Head } from '@inertiajs/vue3'


import { Button } from '@/Components/ui/button'
</script>

<template>
  <Head title="Dashboard" />

  <Layout>
    <template #header>
      <h2
        class="bg-accent text-primary font-semibold text-xl leading-tight py-4 mx-auto sm:px-6 lg:px-8"
      >
        Dashboard Header
      </h2>
    </template>

    <div
      class="p-4"
      v-motion="{
        initial: { scale: 0.95 },
        enter: {
          scale: 1,
          transition: {
            ease: 'easeInOut',
            duration: 500,
          },
        },
      }"
    >
      <div
        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
      >
        <div class="p-6 text-gray-900 dark:text-gray-100">
          <span v-if="$page.props.auth.user.data.first_name"
            >{{ $page.props.auth.user.data.first_name }}
          </span>
          <span class="capitalize" v-else>{{
            $page.props.auth.user.data.username
          }}</span
          >, You're logged in!
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-4 p-4">
      <Card class="p-2 hover:bg-accent" v-for="i in 6">
        <CardHeader>
          <CardTitle>Card Title</CardTitle>
        </CardHeader>
        <CardDescription>
          <p class="text-gray-500">
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia
            harum libero iure rerum minus? Ullam voluptatem nisi facilis qui.
            Facilis consequatur accusamus repellat reiciendis in iusto
            laudantium, blanditiis saepe eum?
          </p>
        </CardDescription>

        <CardFooter class="p-2">
          <Button variant="outline">Button test</Button>
        </CardFooter>
      </Card>
    </div>

    <div class="py-12">
      <div class="sm:px-6" v-for="i in 6" :key="i">
        <TransitionChild
          :show="true"
          appear
          as="div"
          enter="transform transition duration-[400ms]"
          enter-from="opacity-0 rotate-[-120deg] scale-50"
          enter-to="opacity-100 rotate-0 scale-100"
          leave="transform duration-200 transition ease-in-out"
          leave-from="opacity-100 rotate-0 scale-100 "
          leave-to="opacity-0 scale-95 "
        >
          Lorem ipsum dolor sit amet consectetur adipisicing elit. Tempore,
          libero? Explicabo provident, laudantium blanditiis facere enim id sed
          incidunt! Eos tempore maxime accusantium quisquam itaque soluta? Quo
          repudiandae consectetur ab molestias cum veniam, quae praesentium
          doloribus iusto pariatur placeat animi, dolores accusantium nisi
          explicabo corrupti at ut quibusdam illo. Eligendi nemo error earum
          vitae, totam libero dolorum dolor dolorem minima. Quia quos,
          recusandae perspiciatis autem ratione illum dignissimos, architecto
          unde neque cumque maxime? Ipsa nam reprehenderit rerum facere
          repellendus, vitae quam iusto provident recusandae eligendi molestias
          impedit quidem possimus assumenda fugit sunt? Ad enim libero vitae
          beatae magni adipisci, veritatis minima dignissimos dolore sequi vero
          assumenda maiores alias illo atque pariatur ipsa tempore aliquam
          ducimus esse. Est nostrum quia provident. Debitis, amet? Ab aliquid
          ut, facilis suscipit rerum facere. Quam provident nulla harum quis
          assumenda nisi a error eos modi, pariatur hic natus sint suscipit quo
          obcaecati illum rerum sed est ipsam, incidunt ipsa. Quo repellat
          veritatis modi sint ipsam quas necessitatibus minus consequuntur ullam
          quae blanditiis ipsum voluptas, mollitia architecto adipisci et magni
          fugit eligendi excepturi impedit? Iure nobis, hic voluptas odit
          voluptatibus fuga quaerat porro possimus rerum nam accusantium ipsum
          corrupti in similique, tenetur, consectetur id praesentium molestias
          officia? Itaque quod illum, doloribus at dignissimos fugiat expedita
          recusandae illo aperiam quisquam temporibus iusto nisi unde, aliquid a
          corporis iure. Eligendi ullam quasi id tenetur maxime quos veniam
          mollitia! Quisquam optio est hic obcaecati vero aspernatur quibusdam
          voluptatum molestiae adipisci quia amet accusamus consequatur, unde
          sapiente aut modi quae nisi eum dicta sint. Illo fugit rem magni natus
          commodi repudiandae nam porro vitae ipsa obcaecati dolorum eligendi
          animi minus nemo totam adipisci fugiat numquam itaque quo accusantium
          laborum aspernatur, exercitationem quibusdam incidunt? Mollitia
          reiciendis eos facilis quos nam, maiores harum asperiores minima
          temporibus exercitationem quae nemo commodi doloremque modi quia
          beatae doloribus! Accusamus in, consequuntur harum nobis est dolore
          corporis odio id beatae quos ipsa eum itaque illo incidunt esse
          dolores maxime adipisci. Accusamus aliquam hic nobis! Beatae velit id
          laboriosam magnam mollitia deserunt ipsa nostrum non quasi possimus?
          Optio harum quo culpa consectetur neque recusandae reiciendis soluta,
          sapiente dolore quam aliquam dolorum mollitia vitae. Beatae dolore hic
          quis fugit quidem? Veritatis harum beatae ratione accusantium enim
          quasi, laborum asperiores rem nobis cupiditate non saepe perspiciatis.
          Porro officiis accusantium repellat quibusdam velit amet provident
          excepturi minus, aspernatur aperiam repudiandae animi tempore nulla
          eligendi eveniet doloribus dolor nisi nesciunt ipsum natus possimus
          id! Inventore possimus cupiditate architecto odio pariatur at porro
          aperiam! Maiores nobis amet praesentium aliquid consequatur nemo,
          illum ipsum placeat eligendi pariatur obcaecati ut cupiditate odio
          deleniti, officia dolorum iusto iste nam voluptatem eveniet quas.
          Totam quaerat error nulla amet quis vitae deserunt ipsa optio placeat
          atque, deleniti delectus magni expedita temporibus sed excepturi, non,
          omnis iure quidem consectetur neque? Adipisci facere iure debitis
          nihil temporibus corporis nesciunt expedita repellendus, eius, vero
          sapiente eveniet sint aperiam molestias ab similique voluptatum
          voluptas at quam unde asperiores. Perspiciatis, labore ad inventore
          non alias blanditiis at, laborum et nobis nam nemo!
        </TransitionChild>
      </div>
    </div>
  </Layout>
</template>
